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

$Id: ListaFormPDF.class.php 59612 2014-09-02 12:00:51Z gelson $

Casos de uso: uc-01.01.00
*/

/**
    * Especialização da Classe ListaPDF
    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class ListaFormPDF extends ListaPDF
{
  public $stCampoSubTitulo        = '';
  public $arTotalPaginasSubTitulo = array();
  public $boQuebraForcada         = false;

  public function setCampoSubTitulo($stCampo) { $this->stCampoSubTitulo = $stCampo; }
  public function getCampoSubTitulo() { return $this->stCampoSubTitulo ;    }

/**
    * Método Construtor
    * @access Private
*/
function ListaFormPDF($orientation='P',$unit='mm',$format='A4')
{
    parent::ListaPDF( $orientation,$unit,$format );
}

/**
    * Define o cabeçalho de uma lista
    * @access Public
    * @param  String $stRotulo Valor que sera impresso no cabeçao da lista
    * @param  Integer $inLargura Determina a largura da coluna
    * @param  Integer $inTamanho Determina o tamnanho da fonte
    * @param  String $stStyle Determina a formatação da fonte
    * @param  Strind $stFont Determina qual a fonte sera usada
*/
function addCabecalho($stRotulo, $inLargura = "",  $inTamanho = 8 , $stStyle = "" , $stFont = "Arial"  , $stBorder = "0" , $stColor = "255,255,255")
{
    $stAlinhamento = $this->stAlinhamento ? $this->stAlinhamento : "C";
    $this->arCabecalho[$this->inIndiceLista][] = array( "rotulo" => $stRotulo , "tamanho" => $inTamanho ,"style" => $stStyle , "font" => $stFont , "alinhamento" => $stAlinhamento, "borda" => $stBorder, "color" => $stColor );
    $this->arLarguraColuna[$this->inIndiceLista][] =  $inLargura  * ( $this->inLarguraUtil / 100 );
}

/**
    * Define quais campos o do objeto recordset serao usados assim como sua formatação
    * @access Public
    * @param  String $stCampo Valor que sera impresso no corpo da lista
    * @param  Integer $inTamanho Determina o tamnanho da fonte
    * @param  String $stStyle Determina a formatação da fonte
    * @param  Strind $stFont Determina qual a fonte sera usada
    * @param  Strind $stBorder Determina se o campo tem borda (1) ou não (0)
    * @param  Strind $stColor Determina qual a cor em RGB a ser utilizada
    * @param  Strind $inFill Determina se o fundo da célula deve ser preenchido (1) ou transparente (0)
*/
function addCampo($stCampo , $inTamanho = 8 , $stStyle = "" , $stFont = "Arial" , $stBorder = "0", $stColor = "255,255,255" , $inFill = "1")
{
    $stAlinhamento = $this->stAlinhamento ? $this->stAlinhamento : "L";
    $this->arCampo[$this->inIndiceLista][] = array( "campo" => $stCampo, "tamanho" => $inTamanho ,"style" => $stStyle , "font" => $stFont , "alinhamento" => $stAlinhamento , "borda" => $stBorder , "color" => $stColor , "fill" => $inFill );
}

/**
    * Monta o cabeçalho da lista corrente
    * @access Private
    * @param  $inIndiceLista Determina o indice da lista corrente
*/
function montaCabecalhoLista($inIndiceLista)
{
    if ( is_array($this->arLarguraColuna[$inIndiceLista]) ) {
        reset( $this->arLarguraColuna[$inIndiceLista] );
        $x = $this->getX();
        $inAltura = $this->inAlturaCabecalho[$this->inIndiceLista];
        if ( $this->getMultiplo() ) {
            //$this->ln($inAltura + 4);
        }

        $flMultiplicador = 1;
        foreach ($this->arCabecalho[$inIndiceLista] as $arCabecalho) {
            $flLargura = $this->GetStringWidth( str_replace("\r", "",$arCabecalho["rotulo"]) );
            $arLarguraColuna = each($this->arLarguraColuna[$inIndiceLista]  );
            if ($arLarguraColuna["value"] != 0) {
                $flMultiplicadorTmp = (int) ( $flLargura / $arLarguraColuna["value"] ) + 2;
            }
            if ($flMultiplicadorTmp > $flMultiplicador) {
                $flMultiplicador = $flMultiplicadorTmp;
            }
        }

        reset( $this->arCabecalho[$inIndiceLista] );
        reset( $this->arLarguraColuna[$inIndiceLista] );

        if ( $this->verificaQuebraPagina( $inAltura * $flMultiplicador ) ) {
            $this->addPage();
        }
         foreach ($this->arCabecalho[$inIndiceLista] as $arCabecalho) {
           $y = $this->getY();
           $arLarguraColuna = each($this->arLarguraColuna[$inIndiceLista]  );
           $this->SetFont( $arCabecalho["font"], $arCabecalho["style"], $arCabecalho["tamanho"] );
           if ($arCabecalho["color"] != '') {
             $arRGB = explode(",", $arCabecalho["color"]);
             $this->SetFillColor( ($arRGB[0]?$arRGB[0]:'255') , ($arRGB[1]?$arRGB[1]:'255') , ($arRGB[2]?$arRGB[2]:'255') );
           } else {
             $this->SetFillColor( '255' , '255' , '255' );
           }
           $this->MultiCell( $arLarguraColuna["value"] ,$this->inAlturaCabecalho[$inIndiceLista], $arCabecalho["rotulo"], $arCabecalho['borda'], $arCabecalho["alinhamento"], (($arCabecalho["color"]=='255,255,255')?0:1) );
           $x += $arLarguraColuna["value"];
           if ( $this->getY() - $y > $inAltura ) {
               $inAltura = $this->getY() - $y;
           }
          $this->setXY($x, $y );
        }

        if ( $this->getMultiplo() ) {
            //$this->ln($inAltura + 3);
            $this->ln($inAltura);
        } else {
            $this->ln($inAltura);
        }

        reset( $this->arLarguraColuna[$inIndiceLista] );
        $this->setMultiplo( true );
    }
}

/**
    * Monta o PDF conforme as propriedades setadas
    * @access Public
*/
function montaPDF()
{
    if ($this->stFilaImpressao) {
        if( $this->tMargin < 15 )
            $this->tMargin= 15 ;
        if( $this->bMargin < 15 )
            $this->bMargin= 15 ;
    }

    $this->open();
    if ( count( $this->arFiltro ) ) {
        $this->montaFiltro();
    }

    $nbPagina = 1;
    foreach ($this->arRecordSet as $inIndiceLista => $rsRecordSet) {
        $boFlagQuebraLinha = false;
        $boFlagQuebraProximaLinha = false;

        if ( $this->getCampoSubTitulo() != '' ) {
            if ( $rsRecordSet->getCampo ( $this->getCampoSubTitulo() ) ) {

                $this->stSubTitulo = $rsRecordSet->getCampo ( $this->getCampoSubTitulo() );
                if (!$this->arTotalPaginasSubTitulo[$this->stSubTitulo]) {
                    $this->arTotalPaginasSubTitulo[$this->stSubTitulo] = 1;
                } else {
                    $this->arTotalPaginasSubTitulo[$this->stSubTitulo]++;
                }
            }
        }

        if ($this->arQuebraPaginaLista[$inIndiceLista]) {
            $this->arSubTitulos[$nbPagina] = $this->stSubTitulo;
            $this->addPage();
        }

        // Checa se é componente agrupado
        if (is_array($this->arComponenteAgrupado) ) {
          if ( array_key_exists($inIndiceLista, $this->arComponenteAgrupado) ) {
              if ( $this->verificaQuebraPaginaGrupo( $this->arComponenteAgrupado[ $inIndiceLista ] ) ) {
                  $this->addPage();
                  $boFlagQuebraLinha = false;
              }
          }
        }

        $this->montaCabecalhoLista( $inIndiceLista );
        $boIndenta = $this->verificaIndentacao( $inIndiceLista );
        $boQuebraLinha = $this->verificaQuebraLinha( $inIndiceLista );
        $boQuebraProximaLinha = $this->verificaQuebraProximaLinha( $inIndiceLista );
        $boQuebraPagina = $this->verificaQuebraPagina2( $inIndiceLista );
        while ( !$rsRecordSet->eof()  ) {
            $this->boQuebraForcada = false;
            if (count($this->arLarguraColuna[$inIndiceLista])>1) {
              reset( $this->arLarguraColuna[$inIndiceLista] );
            }
            if ($boQuebraLinha and $boFlagQuebraLinha) {
                foreach ($this->arQuebraLinha[$inIndiceLista] as $arQuebraLinha) {
                    if ( $rsRecordSet->getCampo( $arQuebraLinha["campo"] ) == $arQuebraLinha["valor"] ) {
                        $this->ln( $arQuebraLinha["altura"] );
                    }
                    if ( $this->verificaQuebraPagina( $this->inAlturaLinha[$inIndiceLista] ) and !$rsRecordSet->eof() ) {
                        $this->addPage();
                        $this->montaCabecalhoLista( $inIndiceLista );

                    }
                }
            }
            if ( is_array( $this->arCampo[$inIndiceLista] ) ) {
                foreach ($this->arCampo[$inIndiceLista] as $arCampo) {
                    $arLarguraColuna = each( $this->arLarguraColuna[$inIndiceLista] );
                    $stCampoId = $arCampo["campo"];
                    $stId = "";
                    if (strstr($stCampoId,'[') || strstr($stCampoId,']')) {
                        for ($inCount=0; $inCount<strlen($stCampoId); $inCount++) {
                            if ($stCampoId[ $inCount ] == '[') $inInicialId = $inCount;
                            if (($stCampoId[ $inCount ] == ']') && isset($inInicialId) ) {
                                $stId .= $rsRecordSet->getCampo( trim( substr($stCampoId,$inInicialId+1,(($inCount-$inInicialId)-1)) ) );
                                unset($inInicialId);
                            }elseif( !isset($inInicialId) )
                                $stId .= $stCampoId[ $inCount ];
                        }
                    } else {
                        $stId = $rsRecordSet->getCampo( $stCampoId );
                    }
                    $stConteudo = $stId;
                    //Adicionada borda informada pelo usuário
                    //$stBordas = 0;
                    $stBordas = $arCampo["borda"];
                    $this->SetFont( $arCampo["font"], $arCampo["style"], $arCampo["tamanho"] );
                    $stAlinhamento = $arCampo["alinhamento"];
                    if ($boIndenta) {
                        foreach ($this->arIndentaColuna[$inIndiceLista] as $arIndentaColuna) {
                            if ($arCampo["campo"] == $arIndentaColuna["coluna"]) {
                                $stIndenta = "";
                                $inNivel = $rsRecordSet->getCampo( $arIndentaColuna["campo"] );
                                for ($inContNivel = 1 ; $inContNivel < $inNivel; $inContNivel++) {
                                    $stIndenta .= $arIndentaColuna["espaco"];
                                }
                                if ($stAlinhamento == 'R') {
                                    $stConteudo = $stConteudo.$stIndenta;
                                } else {
                                    $stConteudo = $stIndenta.$stConteudo;
                                }
                            }
                        }
                    }
                    if ($arCampo["color"] != '') {
                      $arRGB = explode(",", $arCampo["color"]);
                      $this->SetFillColor( ($arRGB[0]?$arRGB[0]:'255') , ($arRGB[1]?$arRGB[1]:'255') , ($arRGB[2]?$arRGB[2]:'255') );
                    } else {
                      $this->SetFillColor( '255' , '255' , '255' );
                    }
                    $this->Cell( $arLarguraColuna["value"] , $this->inAlturaLinha[$inIndiceLista] ,$stConteudo ,$stBordas ,0 ,$stAlinhamento, $arCampo["fill"] );
                 }
            }
            if ($boQuebraProximaLinha) {
                foreach ($this->arQuebraProximaLinha[$inIndiceLista] as $arQuebraProximaLinha) {
                    if ( $rsRecordSet->getCampo( $arQuebraProximaLinha["campo"] ) == $arQuebraProximaLinha["valor"] ) {
                        $this->ln( $arQuebraProximaLinha["altura"] );
                    }
                    if ( $this->verificaQuebraPagina( $this->inAlturaLinha[$inIndiceLista] ) and !$rsRecordSet->eof() ) {
                        $this->addPage();
                        $this->montaCabecalhoLista( $inIndiceLista );
                    }
                }
            }
            $boFlagQuebraLinha = true;
            $this->ln( $this->inAlturaLinha[$inIndiceLista] );
            if ($boQuebraPagina) {
                foreach ($this->arQuebraPagina[$inIndiceLista] as $arQuebraPagina) {
                    if ( $rsRecordSet->getCampo( $arQuebraPagina["campo"] ) == $arQuebraPagina["valor"] ) {
                        $this->addPage();
                        $this->montaCabecalhoLista( $inIndiceLista );
                        $boFlagQuebraLinha = false;
                    }
                }
            }
            $rsRecordSet->proximo();
            if (is_array($this->arComponenteAgrupado) ) {
              if (array_key_exists($inIndiceLista, $this->arComponenteAgrupado)) {
                if (!$this->arComponenteAgrupado[ $inIndiceLista ]) {
                    if ( $this->verificaQuebraPagina( $this->inAlturaLinha[$inIndiceLista] ) and !$rsRecordSet->eof() ) {
                        $this->boQuebraForcada = true;
                        $this->addPage();
                        $this->montaCabecalhoLista( $inIndiceLista );
                        $boFlagQuebraLinha = false;
                    }
                }
              }
            }
        }
    }
}

}
?>
