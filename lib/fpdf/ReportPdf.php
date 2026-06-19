<?php

require_once('fpdf.php');

/**
 * Formal report PDF layout for E D & C SOLUTIONS documents.
 */
class ReportPdf extends FPDF {

  public function GetPageWidth() {
    return $this->w;
  }

  public function GetPageHeight() {
    return $this->h;
  }

  private $docTitle = '';
  private $docSubtitle = '';

  private $colorNavy    = array(20, 70, 45);       // Deep Forest Green
  private $colorNavyMid = array(35, 105, 70);      // Medium Forest Green
  private $colorAccent  = array(74, 172, 117);     // Premium Sage/Mint Green
  private $colorText    = array(33, 37, 41);
  private $colorMuted   = array(108, 117, 125);
  private $colorLight   = array(243, 247, 244);    // Very light mint/green tint
  private $colorBorder  = array(220, 228, 222);    // Subtle sage-tinted border

  private $orgName    = 'E D & C SOLUTIONS';
  private $orgTagline = 'Personal Data Management System';

  private $tableHeaders = null;
  private $tableColWidths = null;
  private $tableAligns = null;

  public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4') {
    parent::__construct($orientation, $unit, $size);
    $this->SetAutoPageBreak(true, 24);
    $this->AliasNbPages();
  }

  public function setDocumentTitle($title, $subtitle = '') {
    $this->docTitle = $title;
    $this->docSubtitle = $subtitle;
  }

  public function Header() {
    $margin = $this->lMargin;
    $pageWidth = $this->GetPageWidth();
    $rightX = $pageWidth - $this->rMargin;

    $this->SetFillColor($this->colorNavy[0], $this->colorNavy[1], $this->colorNavy[2]);
    $this->Rect(0, 0, $pageWidth, 13, 'F');

    $this->SetFillColor($this->colorAccent[0], $this->colorAccent[1], $this->colorAccent[2]);
    $this->Rect(0, 13, $pageWidth, 1.2, 'F');

    $this->SetXY($margin, 3.2);
    $this->SetFont('Arial', 'B', 10);
    $this->SetTextColor(255, 255, 255);
    $this->Cell(0, 5, $this->orgName, 0, 0, 'L');

    $this->SetFont('Arial', '', 7);
    $this->SetTextColor(190, 200, 215);
    $this->SetXY($margin, 3.2);
    $this->Cell(0, 5, $this->orgTagline, 0, 0, 'R');

    $this->SetY(18);

    if ($this->docTitle !== '') {
      $this->SetFont('Arial', 'B', 13);
      $this->SetTextColor($this->colorNavy[0], $this->colorNavy[1], $this->colorNavy[2]);
      $this->Cell(0, 6, $this->docTitle, 0, 1, 'L');
    }

    if ($this->docSubtitle !== '') {
      $this->SetFont('Arial', '', 8);
      $this->SetTextColor($this->colorMuted[0], $this->colorMuted[1], $this->colorMuted[2]);
      $this->Cell(0, 4, $this->docSubtitle, 0, 1, 'L');
    }

    $y = $this->GetY() + 1.5;
    $this->SetDrawColor($this->colorAccent[0], $this->colorAccent[1], $this->colorAccent[2]);
    $this->SetLineWidth(0.35);
    $this->Line($margin, $y, $margin + 36, $y);
    $this->SetDrawColor($this->colorBorder[0], $this->colorBorder[1], $this->colorBorder[2]);
    $this->SetLineWidth(0.15);
    $this->Line($margin, $y + 0.7, $rightX, $y + 0.7);

    if ($this->PageNo() > 1) {
      $this->SetY(20);
      if ($this->tableHeaders !== null) {
        $this->drawTableHeaderRow();
      }
      return;
    }

    $this->Ln(5);
  }

  public function Footer() {
    $margin = $this->lMargin;
    $rightX = $this->GetPageWidth() - $this->rMargin;

    $this->SetY(-20);
    $this->SetDrawColor($this->colorBorder[0], $this->colorBorder[1], $this->colorBorder[2]);
    $this->Line($margin, $this->GetY(), $rightX, $this->GetY());

    $this->SetY(-16);
    $this->SetFont('Arial', 'I', 6.5);
    $this->SetTextColor($this->colorMuted[0], $this->colorMuted[1], $this->colorMuted[2]);
    $this->Cell(0, 4, 'Confidential — For authorized personnel only. Unauthorized distribution is prohibited.', 0, 0, 'L');

    $this->SetFont('Arial', '', 7);
    $this->Cell(0, 4, 'Page ' . $this->PageNo() . ' of {nb}', 0, 0, 'R');

    $this->SetY(-12);
    $this->SetFont('Arial', '', 6);
    $this->SetTextColor($this->colorMuted[0], $this->colorMuted[1], $this->colorMuted[2]);
    $this->Cell(0, 3, $this->orgName, 0, 0, 'C');
  }

  public function drawMetaBox($rows) {
    $margin = $this->lMargin;
    $width = $this->GetPageWidth() - $margin - $this->rMargin;
    $startY = $this->GetY();
    $rowHeight = 5;
    $padding = 4;
    $boxHeight = count($rows) * $rowHeight + $padding;

    $this->SetFillColor($this->colorLight[0], $this->colorLight[1], $this->colorLight[2]);
    $this->SetDrawColor($this->colorBorder[0], $this->colorBorder[1], $this->colorBorder[2]);
    $this->Rect($margin, $startY, $width, $boxHeight, 'DF');

    $this->SetY($startY + ($padding / 2));
    foreach ($rows as $row) {
      $this->SetX($margin + 4);
      $this->SetFont('Arial', 'B', 7);
      $this->SetTextColor($this->colorMuted[0], $this->colorMuted[1], $this->colorMuted[2]);
      $this->Cell(38, $rowHeight, strtoupper($row[0]) . ':', 0, 0, 'L');
      $this->SetFont('Arial', '', 8);
      $this->SetTextColor($this->colorText[0], $this->colorText[1], $this->colorText[2]);
      $this->Cell(0, $rowHeight, $row[1], 0, 1, 'L');
    }

    $this->SetY($startY + $boxHeight + 5);
  }

  public function drawSectionHeader($number, $title) {
    $this->checkPageBreak(14);

    $this->SetFillColor($this->colorNavyMid[0], $this->colorNavyMid[1], $this->colorNavyMid[2]);
    $this->SetFont('Arial', 'B', 8);
    $this->SetTextColor(255, 255, 255);
    $this->Cell(7, 7, $number, 0, 0, 'C', true);

    $this->SetFillColor($this->colorLight[0], $this->colorLight[1], $this->colorLight[2]);
    $this->SetTextColor($this->colorNavy[0], $this->colorNavy[1], $this->colorNavy[2]);
    $this->SetFont('Arial', 'B', 9);
    $this->Cell(0, 7, '  ' . $title, 0, 1, 'L', true);
    $this->Ln(3);
  }

  public function drawDetailRows($details) {
    $labelWidth = 50;

    foreach ($details as $idx => $row) {
      $this->checkPageBreak(11);

      $fill = ($idx % 2 === 0);
      if ($fill) {
        $this->SetFillColor($this->colorLight[0], $this->colorLight[1], $this->colorLight[2]);
      } else {
        $this->SetFillColor(255, 255, 255);
      }

      $this->SetDrawColor($this->colorBorder[0], $this->colorBorder[1], $this->colorBorder[2]);
      $this->SetFont('Arial', 'B', 8);
      $this->SetTextColor($this->colorMuted[0], $this->colorMuted[1], $this->colorMuted[2]);
      $this->Cell($labelWidth, 9, '  ' . strtoupper($row[0]), 'LTB', 0, 'L', $fill);

      $this->SetFont('Arial', '', 9);
      $this->SetTextColor($this->colorText[0], $this->colorText[1], $this->colorText[2]);

      if (!empty($row[2]) && $row[2] === 'multiline') {
        $margin = $this->lMargin;
        $valueWidth = $this->GetPageWidth() - $margin - $this->rMargin - $labelWidth;
        $this->MultiCell($valueWidth, 9, '  ' . $row[1], 'RTB', 'L', $fill);
      } else {
        $this->Cell(0, 9, '  ' . $row[1], 'RTB', 1, 'L', $fill);
      }
    }
  }

  public function beginTable($headers, $colWidths, $aligns = null) {
    $this->tableHeaders = $headers;
    $this->tableColWidths = $colWidths;
    $this->tableAligns = $aligns;

    if ($aligns === null) {
      $this->tableAligns = array_fill(0, count($headers), 'C');
    }

    $this->drawTableHeaderRow();
  }

  public function endTable() {
    $this->tableHeaders = null;
    $this->tableColWidths = null;
    $this->tableAligns = null;
  }

  private function drawTableHeaderRow() {
    if ($this->tableHeaders === null) {
      return;
    }

    $this->SetFont('Arial', 'B', 7);
    $this->SetFillColor($this->colorNavyMid[0], $this->colorNavyMid[1], $this->colorNavyMid[2]);
    $this->SetTextColor(255, 255, 255);
    $this->SetDrawColor($this->colorBorder[0], $this->colorBorder[1], $this->colorBorder[2]);

    foreach ($this->tableHeaders as $i => $header) {
      $this->Cell($this->tableColWidths[$i], 8, $header, 1, 0, $this->tableAligns[$i], true);
    }
    $this->Ln();
  }

  public function drawTableRow($cells, $rowIndex) {
    if ($this->tableColWidths === null) {
      return;
    }

    $this->checkPageBreak(9);

    $fill = ($rowIndex % 2 === 0);
    if ($fill) {
      $this->SetFillColor($this->colorLight[0], $this->colorLight[1], $this->colorLight[2]);
    } else {
      $this->SetFillColor(255, 255, 255);
    }

    $this->SetFont('Arial', '', 7.5);
    $this->SetTextColor($this->colorText[0], $this->colorText[1], $this->colorText[2]);
    $this->SetDrawColor($this->colorBorder[0], $this->colorBorder[1], $this->colorBorder[2]);

    foreach ($cells as $i => $cell) {
      $align = isset($this->tableAligns[$i]) ? $this->tableAligns[$i] : 'L';
      $this->Cell($this->tableColWidths[$i], 7, $cell, 1, 0, $align, $fill);
    }
    $this->Ln();
  }

  public function drawEmptyNotice($message) {
    $this->SetFont('Arial', 'I', 8);
    $this->SetTextColor($this->colorMuted[0], $this->colorMuted[1], $this->colorMuted[2]);
    $this->Cell(0, 8, $message, 0, 1, 'L');
  }

  public function drawApprovalStamp($status) {
    $stampWidth = 42;
    $x = $this->GetPageWidth() - $this->rMargin - $stampWidth;
    $y = 46;
    $savedY = $this->GetY();

    $this->SetXY($x, $y);
    $this->SetFillColor($this->colorLight[0], $this->colorLight[1], $this->colorLight[2]);
    $this->SetDrawColor($this->colorAccent[0], $this->colorAccent[1], $this->colorAccent[2]);
    $this->SetLineWidth(0.3);
    $this->Rect($x, $y, $stampWidth, 14, 'DF');
    $this->SetLineWidth(0.15);

    $this->SetXY($x, $y + 2);
    $this->SetFont('Arial', 'B', 7);
    $this->SetTextColor($this->colorMuted[0], $this->colorMuted[1], $this->colorMuted[2]);
    $this->Cell($stampWidth, 4, 'RECORD STATUS', 0, 1, 'C');

    $this->SetX($x);
    $this->SetFont('Arial', 'B', 9);
    $this->SetTextColor($this->colorAccent[0], $this->colorAccent[1], $this->colorAccent[2]);
    $this->Cell($stampWidth, 5, strtoupper($status), 0, 1, 'C');

    $this->SetY($savedY);
  }

  public function checkPageBreak($neededHeight = 10) {
    if ($this->GetY() + $neededHeight > $this->PageBreakTrigger) {
      $this->AddPage();
      return true;
    }
    return false;
  }

  public function truncate($text, $maxLen) {
    $text = (string) $text;
    if (strlen($text) > $maxLen) {
      return substr($text, 0, $maxLen - 2) . '..';
    }
    return $text;
  }
}
