<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/
define('FPDF_FONTPATH',CAM_FPDF.'fonts/');
include(CAM_FPDF.'fpdf.php');

class arquivoPdf extends FPDF
{
    public $sLogoPrefeitura,
        $sNomePrefeitura,
        $sEnderecoPrefeitura,
        $sModulo,
        $sTitulo,
        $sSubTitulo,
        $sNomeRelatorio,
        $sData,
        $sHora,
        $sImprimeUsuario,
        $sUsuario;

    //Cabeçalho
    public function Header()
    {
        $this->SetCreator = "URBEM";
        $this->SetFillColor(220);
        $tMargem = $this->tMargin;
        $lMargem = $this->lMargin;
        $this->Image($this->sLogoPrefeitura,$lMargem,$tMargem,20);
        $this->Cell(20,10,'');
        $this->SetFont('Helvetica','B',8);
        $this->SetFillColor(255);
        $X = $this->GetX();
        $Y = $this->GetY();
        $this->Cell(70,4,$this->sNomePrefeitura,0,'L',1);
        $this->SetFont('Helvetica','',8);
        $this->SetXY($X,$Y+4);
        $this->Cell(70,4,$this->sEnderecoPrefeitura[0],0,'L',1);
        $this->SetXY($X,$Y+8);
        $this->Cell(70,4,$this->sEnderecoPrefeitura[1],0,'L',1);
        $this->SetXY($X,$Y+12);
        $this->Cell(70,4,$this->sEnderecoPrefeitura[2],0,'L',1);
        $this->SetXY($X,$Y+16);
        $this->Cell(70,4,$this->sEnderecoPrefeitura[3],0,'L',1);
        $this->SetFont('Helvetica','B',8);
        $sDisp = $this->DefOrientation;
        $iAjus = 70;
        if ($sDisp=='L') {
            $iAjus = 160;
        }
        $this->SetXY($X+$iAjus,$Y);
        $this->SetFillColor(220);
        $this->Cell(0,5,$this->sModulo,1,0,'L',1);
        $this->SetXY($X+$iAjus,$Y+5);
        $this->Cell(0,5,$this->sTitulo,1,'TRL','L',1);
        $this->SetXY($X+$iAjus,$Y+10);
        $this->SetFont('Helvetica','',8);
        if ($this->sImprimeUsuario=='N') {
            $this->Cell(0,5,$this->sSubTitulo,1,'RLB','L',1);
        } else {
            $this->Cell(56,5,$this->sSubTitulo,1,'RLB','L',1);
            $this->Cell(0,5,"Usuário: ".$this->sUsuario,1,'RLB','L',1);
        }
        $this->SetXY($X+$iAjus,$Y+15);
        $this->Cell(33,5,'Emissão: '.$this->sData,1,0,'L',1);
        $this->Cell(23,5,'Hora: '.$this->sHora,1,0,'L',1);
        $this->Cell(0,5,'Página: '.$this->PageNo().' de {nb}',1,0,'L',1);
        $this->Ln(4);
        $this->Cell(0,1,' ','B',0,'C');
        $this->Ln(3);
    }

    //Rodapé
    public function Footer()
    {
        $sDisp = $this->DefOrientation;
        $iAjus = -20;
        if ($sDisp=='L') {
            $iAjus = -15;
        }
        $this->SetY($iAjus);
        $this->SetFont('Helvetica','',6);
        $this->Cell(0,5,'URBEM - CNM Confederação Nacional de Municípios - www.cnm.org.br','T',0,'L');
        $this->Cell(0,5,basename($this->sNomeRelatorio),'T',0,'R');
    }
}
