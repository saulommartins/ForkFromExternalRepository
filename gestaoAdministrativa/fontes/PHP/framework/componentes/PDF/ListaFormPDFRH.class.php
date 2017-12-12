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
* Data de Criação: 30/05/2006

* @author Desenvolvedor: Diego Lemos de Souza
* @author Documentor: Diego Lemos de Souza

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

/**
    * Especialização da Classe ListaPDF
    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class ListaFormPDFRH extends ListaFormPDF
{
/**
    * Método Construtor
    * @access Private
*/
function ListaFormPDFRH($orientation='P',$unit='mm',$format='A4')
{
    parent::ListaFormPDF( $orientation,$unit,$format );
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
        foreach ($this->arCabecalho[$inIndiceLista] as $arCabecalho) {
            if ($arCabecalho['rotulo'] == "") {
                $boVazio = true;
            } else {
                $boVazio = false;
                break;
            }
        }
        if ($boVazio == false) {
            $x = $this->getX();
            $inAltura = $this->inAlturaCabecalho[$this->inIndiceLista];
            if ( $this->getMultiplo() ) {
                $this->ln($inAltura + 4);
            }
            //if ( $this->verificaQuebraPagina( $inAltura ) ) {
            if ( $this->verificaQuebraPagina( $this->inAlturaCabecalho[$inIndiceLista]?$this->inAlturaCabecalho[$inIndiceLista]:5) ) {

                $this->addPage();
            }
             foreach ($this->arCabecalho[$inIndiceLista] as $arCabecalho) {
               $y = $this->getY();
               $arLarguraColuna = each($this->arLarguraColuna[$inIndiceLista]  );
               $this->SetFont( $arCabecalho["font"], $arCabecalho["style"], $arCabecalho["tamanho"] );
               $arRGB = explode(",", $arCabecalho["color"]);
               $this->SetFillColor( ($arRGB[0]?$arRGB[0]:'255') , ($arRGB[1]?$arRGB[1]:'255') , ($arRGB[2]?$arRGB[2]:'255') );
               $this->MultiCell( $arLarguraColuna["value"] ,$this->inAlturaCabecalho[$inIndiceLista], $arCabecalho["rotulo"].$in, $arCabecalho['borda'], $arCabecalho["alinhamento"], (($arCabecalho["color"]=='255,255,255')?0:1) );
               $x += $arLarguraColuna["value"];
               if ( $this->getY() - $y > $inAltura ) {
                   $inAltura = $this->getY() - $y;
               }
              $this->setXY($x, $y );
            }

            if ( $this->getMultiplo() ) {
                $this->ln($inAltura + 3);
                //$this->ln($inAltura);
            } else {
                $this->ln($inAltura);
            }

            reset( $this->arLarguraColuna[$inIndiceLista] );
            $this->setMultiplo( true );
        }
    }

    return $boVazio;
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
    foreach ($this->arRecordSet as $inIndiceLista => $rsRecordSet) {
        $boFlagQuebraLinha = false;
        $boFlagQuebraProximaLinha = false;
        if ($this->arQuebraPaginaLista[$inIndiceLista]) {
            $this->addPage();
        }

        // Checa se é componente agrupado
        if ($this->arComponenteAgrupado[ $inIndiceLista ]) {
            if ( $this->verificaQuebraPaginaGrupo( $this->arComponenteAgrupado[ $inIndiceLista ] ) ) {
                $this->addPage();
                $boFlagQuebraLinha = false;
            }
        }

        $boVazio = $this->montaCabecalhoLista( $inIndiceLista );
        if (!$boVazio) {
            $boIndenta = $this->verificaIndentacao( $inIndiceLista );
            $boQuebraLinha = $this->verificaQuebraLinha( $inIndiceLista );
            $boQuebraProximaLinha = $this->verificaQuebraProximaLinha( $inIndiceLista );
            $boQuebraPagina = $this->verificaQuebraPagina2( $inIndiceLista );
        }
        while ( !$rsRecordSet->eof()  ) {
            reset( $this->arLarguraColuna[$inIndiceLista] );
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
                    $arRGB = explode(",", $arCampo["color"]);
                    $this->SetFillColor( ($arRGB[0]?$arRGB[0]:'255') , ($arRGB[1]?$arRGB[1]:'255') , ($arRGB[2]?$arRGB[2]:'255') );
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
            if (!$this->arComponenteAgrupado[ $inIndiceLista ]) {
                if ( $this->verificaQuebraPagina( $this->inAlturaLinha[$inIndiceLista] ) and !$rsRecordSet->eof() ) {
                    $this->addPage();
                    $this->montaCabecalhoLista( $inIndiceLista );
                    $boFlagQuebraLinha = false;
                }
            }
        }
    }
}

}
?>
