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
* Montar o HTML de uma litsa de registros armazenada em um array
* Data de Criação: 06/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

* $Id: Lista.class.php 65805 2016-06-17 17:32:03Z franver $

* Casos de uso: uc-01.01.00

*/
include_once( CLA_RECORDSET );
include_once( CLA_TABELA );
include_once( CLA_PAGINACAO );

/**
    *  Classe que gera o HTML da Lista

    * @package framework
    * @subpackage componentes
*/
class Lista extends Tabela
{
/**
    * @access Private
    * @var Array
*/
var $arAcao;

/**
    * @access Private
    * @var Object
*/
var $ultimaAcao;

/**
    * @access Private
    * @var Array
*/
var $arCabecalho;

/**
    * @access Private
    * @var Object
*/
var $ultimoCabecalho;

/**
    * @access Private
    * @var String
*/
var $stClassTitulo;

/**
    * @access Private
    * @var String
*/
var $stClassCabecalho;

/**
    * @access Private
    * @var String
*/
var $stClassContador;

/**
    * @access Private
    * @var String
*/
var $stClassPaginacao;

/**
    * @access Private
    * @var Array
*/
var $arDado;

/**
    * @access Private
    * @var Object
*/
var $ultimoDado;

/**
    * @access Private
    * @var Object
*/
var $obRecordSet;

/**
    * @access Private
    * @var Object
*/
var $obPaginacao;

/**
    * @access Private
    * @var String
*/
var $stTitulo;

/**
    * @access Private
    * @var Boolean
*/
var $boMostraPaginacao;

/**
    * @access Private
    * @var Boolean
*/
var $boMostraSelecionaTodos;
/**
    * @access Private
    * @var Boolean
*/
var $stTotaliza;

/**
    * @access Private
    * @var String
*/
var $stDefinicao;
/**
    * @access Private
    * @var Boolean
*/
var $boNull;
/**
    * @access Private
    * @var String
*/
var $stAjuda;
/**
    * @access Private
    * @var String
*/
var $boNumeracao;
/**
    * @access Private
    * @var boolean
*/
var $boAlternado;
/**
    * @access Private
    * @var String
*/
var $stCampoAgrupar;
/**
    *@access Private
    *@var Array
*/
var $arTotaliza;
/**
    * @access Private
    * @var String
*/
var $stRotuloSomatorio;

var $boEncRegistroUnico;

/**
    * @access Private
    * @var String
*/
var $stClassColuna;

var $boColunas;

var $colunas;

var $cor;

var $stCampoAgrupado;

//SETTERS
/**
    * @access Public
    * @param Array $valor
*/
function setAcao($valor) { $this->arAcao               = $valor;   }

/**
    * @access Public
    * @param Object $valor
*/
function setUltimaAcao($valor) { $this->ultimaAcao           = $valor;   }
/**
    * @access Public
    * @param Object $valor
*/
function setAjuda($valor) { $this->stAjuda         = $valor;         }

/**
    * @access Public
    * @param String $valor
*/
function setCabecalho($valor) { $this->arCabecalho          = $valor;   }

/**
    * @access Public
    * @param Object $valor
*/
function setUltimoCabecalho($valor) { $this->ultimoCabecalho      = $valor;   }

/**
    * @access Public
    * @param String $valor
*/
function setClassTitulo($valor) { $this->stClassTitulo        = $valor;   }

/**
    * @access Public
    * @param String $valor
*/
function setClassCabecalho($valor) { $this->stClassCabecalho     = $valor;   }

/**
    * @access Public
    * @param String $valor
*/
function setClassContador($valor) { $this->stClassContador      = $valor;   }

/**
    * @access Public
    * @param String $valor
*/
function setClassPaginacao($valor) { $this->stClassPaginacao     = $valor;   }

/**
    * @access Public
    * @param Array $valor
*/
function setDado($valor) { $this->arDado               = $valor;   }

/**
    * @access Public
    * @param Object $valor
*/
function setUltimoDado($valor) { $this->ultimoDado           = $valor;   }

/**
    * @access Public
    * @param Object $valor
*/
function setRecordSet($valor) { $this->obRecordeSet         = $valor;   }

/**
    * @access Public
    * @param Object $valor
*/
function setPaginacao($valor) { $this->obPaginacao          = $valor;   }

/**
    * @access Public
    * @param String $valor
*/
function setTitulo($valor) { $this->stTitulo             = $valor;   }

/**
    * @access Public
    * @param Boolean $valor
*/
function setMostraPaginacao($valor) { $this->boMostraPaginacao    = $valor;   }

/**
    * @access Public
    * @param Boolean $valor
*/
function setMostraScroll($inTamanho) { $this->inTamanhoScroll = $inTamanho; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setMostraSelecionaTodos($valor) { $this->boMostaSelecionaTodos = $valor; }
/**
    * @access Public
    * @param String  $valor
*/
function setTotaliza($valor) { $this->stTotaliza = $valor;                 }
/**
    * @access Public
    * @param String $valor
*/
function setDefinicao($valor) { $this->stDefinicao        = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setNull($valor) { $this->boNull     = $valor;                 }
function setObrigatorio($valor) { $this->boNull = !$valor;                 }
function setNumeracao($valor) { $this->boNumeracao = $valor;         }
function setAlternado($valor) { $this->boAlternado = $valor;         }
function setCampoAgrupado($valor) { $this->stCampoAgrupado = $valor;     }
/**
    * @access Public
    * @param Array  $valor
*/
function setTotalizaMultiplo($valor) { $this->arTotaliza = $valor;         }
/**
    * @access Public
    * @param String $valor
*/
function setRotuloSomatorio($valor) { $this->stRotuloSomatorio        = $valor; }

//GETTERS
/**
    * @access Public
    * @return String
*/
function getAcao() { return $this->arAcao;                       }

/**
    * @access Public
    * @return Object
*/
function getUltimaAcao() { return $this->ultimaAcao;                   }
/**
    * @access Public
    * @return Object
*/
function getAjuda() { return $this->stAjuda;                      }

/**
    * @access Public
    * @return String
*/
function getCabecalho() { return $this->arCabecalho;                  }

/**
    * @access Public
    * @return Object
*/
function getUltimoCabecalho() { return $this->ultimoCabecalho;              }

/**
    * @access Public
    * @return String
*/
function getClassTitulo() { return $this->stClassTitulo;                }

/**
    * @access Public
    * @return String
*/
function getClassCabecalho() { return $this->stClassCabecalho;             }

/**
    * @access Public
    * @return String
*/
function getClassContador() { return $this->stClassContador;              }

/**
    * @access Public
    * @return String
*/
function getClassPaginacao() { return $this->stClassPaginacao;             }

/**
    * @access Public
    * @return String
*/
function getDado() { return $this->arDado;                       }

/**
    * @access Public
    * @return Object
*/
function getUltimoDado() { return $this->ultimoDado;                   }

/**
    * @access Public
    * @return Object
*/
function getRecordSet() { return $this->obRecordeSet;                 }

/**
    * @access Public
    * @return Object
*/
function getPaginacao() { return $this->obPaginacao;                  }

/**
    * @access Public
    * @return String
*/
function getTitulo() { return $this->stTitulo;                     }

/**
    * @access Public
    * @return Boolean
*/
function getMostraPaginacao() { return $this->boMostraPaginacao;            }

/**
    * @access Public
    * @return Boolean
*/
function getMostraScroll() { return $this->inTamanhoScroll;                 }

/**
    * @access Public
    * @return Boolean
*/
function getMostraSelecionaTodos() { return $this->boMostaSelecionaTodos;   }
/**
    * @access Public
    * @return Boolean
*/
function getTotaliza() { return $this->stTotaliza; }
/**
    * @access Public
    * @return String
*/
function getDefinicao() { return $this->stDefinicao;                  }
function getNull() { return $this->boNull;                       }
function getObrigatorio() { return !$this->boNull;                      }

function getNumeracao() { return $this->boNumeracao;                    }

function getAlternado() { return $this->boAlternado;                    }
function getCampoAgrupado() { return $this->stCampoAgrupado;            }

/**
    *@access Public
    *@return Array
*/
function getTotalizaMultiplo() { return $this->arTotaliza;                   }
/**
    * @access Public
    * @return String
*/
function getRotuloSomatorio() { return $this->stRotuloSomatorio;                  }

/**
    * Método construtor
    * @access Public
*/
function Lista()
{
    parent::Tabela();
    $this->setCellPadding   ( 2 );
    $this->setCellSpacing   ( 2 );
    $this->setClassTitulo   ( "alt_dados" );
    $this->setClassCabecalho( "labelcentercabecalho" );
    $this->setClassContador( "show_dados_center_bold" );
    $this->setClassPaginacao( "show_dados_center_bold" );
    $arCabecalho = array();
    $arDado = array();
    $obRecordSet = new RecordSet;
    $this->setCabecalho( $arCabecalho );
    $this->setDado( $arDado );
    $this->setRecordSet( $obRecordSet );
    $obPaginacao = new Paginacao;
    $this->setPaginacao( $obPaginacao );
    $this->setTitulo( "Registros" );
    $this->setMostraPaginacao( true );
    $this->setMostraSelecionaTodos( false );
    $this->setMostraScroll( 0 );
    $this->setAlternado( true );

    $this->setDefinicao( 'LISTA' );
    $this->setNull     ( true    );

    $this->setNumeracao( true );
    $this->setRotuloSomatorio("Somatório");
    $this->boEncRegistroUnico = true;
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param String $parametro
    * @param Integer $inWidth
    * @param Boolean $boQuebraLinha
*/
function addCabecalho($parametro = false , $inWidth = 10, $boQuebraLinha = false)
{
    $obCabecalho = new Cabecalho;
    if (is_bool($parametro)) {
        $obCabecalho->setQuebraLinha( $parametro );
        $this->setUltimoCabecalho( $obCabecalho );
    } else {
        $obCabecalho->setQuebraLinha($boQuebraLinha);
        $obCabecalho->addConteudo($parametro);
        $obCabecalho->setWidth($inWidth);
        $this->setUltimoCabecalho( $obCabecalho );
        $this->commitCabecalho();
    }
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function commitCabecalho()
{
    $arCabecalho = $this->getCabecalho();
    $arCabecalho[] = $this->getUltimoCabecalho();
    $this->setCabecalho( $arCabecalho );
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function addDado()
{
    $obDado = new Dado;
    $this->setUltimoDado( $obDado );
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function commitDado()
{
    $arDado = $this->getDado();
    $arDado[] = $this->getUltimoDado();
    $this->setDado( $arDado );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obComponente
    * @param Boolean $obComponente
*/
function addDadoComponente($obComponente , $boNameSequencial = true)
{
    $obDadoComponente = new DadoComponente( $obComponente );
    $obDadoComponente->setNameSequencial( $boNameSequencial );
    $this->setUltimoDado( $obDadoComponente );
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function commitDadoComponente()
{
    $arDadoComponente   = $this->getDado();
    $arDadoComponente[] = $this->getUltimoDado();
    $this->setDado( $arDadoComponente );
}
/* FIM TESTE */

/**
    * FALTA DESCRICAO
    * @access Public
*/
function addAcao()
{
    $obAcao = new Acao;
    $this->setUltimaAcao( $obAcao );
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function commitAcao()
{
    $arAcao = $this->getAcao();
    $arAcao[] = $this->getUltimaAcao();
    $this->setAcao( $arAcao );
}

/**
    * Monta o HTML do Objeto Lista
    * @access Protected
*/
function montaHTML()
{
    include_once 'Ajuda.class.php';
    if($this->getObrigatorio())
       $this->setTitulo('*'.$this->getTitulo());
    $obRecordSet = $this->getRecordSet();
    $arCabecalho = $this->getCabecalho();
    $arDado      = $this->getDado();
    $inNumDados  = count( $arDado );
    $inNumCel    = count( $arDado );
    $this->obPaginacao->setRecordSet( $obRecordSet );
    //CASO EXISTA ALGUMA LINHA SETADA FICA GUARDADA NESTA VARIAVEL
    $arLinha     = $this->getLinha();
    $arAcao      = $this->getAcao();
    $stCaso = substr( $this->getAjuda(),3 );
    $arCasoUso = explode(".",$stCaso);
    if ($this->getAjuda()) {
           $obAjuda = new Ajuda;
           $obAjuda->setCodGestao($arCasoUso[0]);
           $obAjuda->setCodModulo(Sessao::read('modulo'));
           $obAjuda->setCasoUso($this->getAjuda());
           $obAjuda->montaHTML();
           $obAjuda->show();
    }
    //ZERA A LINHA
    $this->setLinha( array() );

    //VERIFICA SE EXISTEM COLSPANS. CASO EXISTAM, ACRESCENTA NO TITULO
    $inCountColSpan = 0;
    if ( count($arCabecalho) ) {
        foreach ($arCabecalho as $obCabecalho) {
            if($obCabecalho->getColSpan() >= 1)
                $inCountColSpan += ($obCabecalho->getColSpan()-1);
        }
    }

    //MONTA TITULO
    $this->addLinha();
    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setClass( $this->getClassTitulo() );
    $this->ultimaLinha->ultimaCelula->setColSpan( count( $arCabecalho ) + $inCountColSpan );
    $this->ultimaLinha->ultimaCelula->addConteudo( $this->getTitulo() );
    $this->ultimaLinha->commitCelula();
    $this->commitLinha();

    //MONTA O CABECALHO
    if ( count($arCabecalho) ) {
        if ($this->getMostraScroll() && !$obRecordSet->eof() ) {
           $this->addDiv(1, 'scrollCabecalho', 'height:30px;overflow-y:scroll');
        }
        $boPrimeiroCab = true; // defini primeiro cabecalho
        $this->addLinha();
        foreach ($arCabecalho as $obCabecalho) {
            if ( $obCabecalho->getQuebraLinha() ) {
                $this->commitLinha();
                $this->addLinha();
            }
            // combo para seleção de registros por pagina
            if ( $boPrimeiroCab && $this->getMostraPaginacao()) {
                $boPrimeiroCab = false;

                $obRegPagina = new Select();
                $obRegPagina->setId('cmbRegPorPagina');
                $obRegPagina->setName('cmbRegPorPagina');

                if (isset($_COOKIE['sw_Max_Linhas'])) {
                    $obRegPagina->setValue($_COOKIE['sw_Max_Linhas']); // guarda em cookie o que o user seleciona
                }

                for ($i=10;$i<51;$i += 10) { // inicia em 10 vai até 50, de 10 em 10
                    $obCurrentOption = new Option;
                    $obCurrentOption->setValor( $i );
                    $obCurrentOption->setCampo( $i );
                    $obCurrentOption->setTitle( 'Mostrar $i registros por pagina');
                    $obRegPagina->arOption[] = $obCurrentOption;
                    unset($obCurrentOption);
                }
                // gera links de paginacao antes
                $this->obPaginacao->geraStrLinks();
                $this->obPaginacao->geraHrefLinks();
                // obtem pagina atual
                $stLinkPgAtual = $this->obPaginacao->arLinksPaginas[0];
                $stLinkPgAtual = str_replace("\"","",$stLinkPgAtual);

                // grava cookie com valor selecionado e da um refresh na tela
                $obRegPagina->obEvento->setOnChange
                  (
                    "var stLinkPgAtual = '".$stLinkPgAtual."';".
                    "document.cookie = 'sw_Max_Linhas = '+this.value; ".
                    "document.location = stLinkPgAtual;"
                  );
                $obRegPagina->montaHTML();

                $obCabecalho->setConteudo($obRegPagina->getHTML());
            }

            $this->ultimaLinha->setUltimaCelula( $obCabecalho );
            $this->ultimaLinha->commitCelula();
        }
        $this->commitLinha();
        if ($this->getMostraScroll() && !$obRecordSet->eof() ) {
            $this->fechaDiv();
        }
    }

    //SETA AS LINHAS QUE ESTAVAM GUARDADAS
    if ( count( $arLinha ) ) {
        foreach ($arLinha as $obLinha) {
            $this->setUltimaLinha( $obLinha );
            $this->commitLinha();
        }
    }

    //GERA A LISTA
    if ( !$obRecordSet->eof() ) {

        $inCont = 0;
        $inContador = $this->obPaginacao->geraContador();
        $obRecordSet->setCorrente( $inContador );
        if ( !$this->getMostraPaginacao() ) {
            $this->obPaginacao->setMaxLinhas( $obRecordSet->getNumLinhas() );
        }
        if ($this->getMostraScroll()) {
            $this->addDiv(2, 'scrollDados', 'height:'.$this->inTamanhoScroll.';overflow-y:scroll');
        }
        $inCountAlternado = 0;
        $campoAgrupadoAnterior = "";
        while ( !$obRecordSet->eof() and $inCont < $this->obPaginacao->getMaxLinhas() ) {
            $inCont++;
            if ($inNumDados) {
                $this->addLinha();
                if ( $this->getNumeracao() ) {
                    $this->ultimaLinha->addCelula();
                    $this->ultimaLinha->ultimaCelula->setClass( $this->getClassContador() );
                    $this->ultimaLinha->ultimaCelula->addConteudo( $inContador );
                    if($this->getMostraScroll())
                       $this->ultimaLinha->ultimaCelula->setWidth( $arCabecalho[0]->getWidth());
                    $this->ultimaLinha->commitCelula();
                }
                $inContDado = 0;
                /* refente a layout alternado (zebra) e agrupamento **********************************************/
                if ( $this->getAlternado() ) {

                    if ( $this->getCampoAgrupado() ) {
                        if ( $obRecordSet->getCampo( $this->getCampoAgrupado() ) == $campoAgrupadoAnterior) {
                            $campoAgrupadoAnterior = $obRecordSet->getCampo( $this->getCampoAgrupado() );
                            $inCountAlternado = $inCountAlternado == 0 ? 0 : 1;
                        } else {
                            $campoAgrupadoAnterior = $obRecordSet->getCampo( $this->getCampoAgrupado() );
                            $inCountAlternado = $inCountAlternado == 0 ? 1 : 0;
                        }
                    } else {
                        if ($inCountAlternado == 1) {
                            $inCountAlternado = 0;
                        } else {
                            $inCountAlternado = 1;
                        }
                    }
                }

                $boSomatorio = false;
                foreach ($arDado as $arObDado) {
                    ##Totalizar
                    if ($arObDado->getTipoTotalizar()) {
                        $boSomatorio = true;
                    } else {
                        $arTotalizar[$arObDado->getCampo()][0] = "&nbsp;";
                    }
                    switch ($arObDado->getTipoTotalizar()) {
                        case "somar":
                            if (strpos($obRecordSet->getCampo($arObDado->getCampo()),',')!==false) {
                                //Valor formatado
                                $arTotalizar[$arObDado->getCampo()][0] += str_replace(',','.',str_replace('.','',$obRecordSet->getCampo($arObDado->getCampo())));
                            } else {
                                //Valor não formatado
                                $arTotalizar[$arObDado->getCampo()][0] += $obRecordSet->getCampo($arObDado->getCampo());
                            }
                            $arTotalizar[$arObDado->getCampo()][1] = $obRecordSet->arFormatacao[$arObDado->getCampo()];
                            break;
                        case "contar":
                            $arTotalizar[$arObDado->getCampo()][0]++;
                            $arTotalizar[$arObDado->getCampo()][1] = "";
                            break;
                    }
                    ##Totalizar
                    $inContDado++;
                    $stCampo = $arObDado->getCampo();
                    if($this->getMostraScroll())
                        $arObDado->setWidth($arCabecalho[$inContDado]->getWidth());
                    $stConteudo = $obRecordSet->retornaValoresRecordSet( $stCampo );
                    $arObDado->setTitleValue( $obRecordSet->retornaValoresRecordSet( $arObDado->getTitle() ) );
                    if ( $arObDado->getMascara() ) {
                        $stMascara = $arObDado->getMascara();
                        include( K_INCLUDES."mascaraListas.inc.php" );
                        $obMascara->setDesmascarado( $stConteudo );
                        $obMascara->mascaraDinamica();
                        $stConteudo = $obMascara->getMascarado();
                    }

                    if ($inCountAlternado == 1) {
                        $this->setClassContador("contadorEscuro");
                        $arObDado->setStyle ("background:#edf4fa;");
                        $this->setStyle("background:#ffffff;");
                    } else {
                        if ($this->getAlternado()) {
                            $this->setClassContador("contadorClaro");
                            $arObDado->setStyle ("background:#d0e4f2;");
                            $this->setStyle("background:#ffffff;");

                        } else {
                             $arObDado->setStyle ("background:#edf4fa;");
                             $this->setStyle("background:#ffffff;");
                        }
                    }

                    //FIXME : correcao temporaria para php4 e 5
                    $arObDado = clone $arObDado;

                    if (strtolower(get_class($arObDado))=='dadocomponente') {
                        //FIXME : correcao temporaria para php4 e 5
                        $arObDado->obComponenteLista = clone $arObDado->obComponenteLista;

                        // VERIFICACAO DE EDICAO OU NAO DOS COMPONENTES INCLUÍDOS NA LISTA //
                        if ( $obRecordSet->getCampo('disabled') != '' ) {
                            $arObDado->obComponenteLista->setDisabled( true );
                            $arObDado->obComponenteLista->setReadOnly( true );
                        }

                        if ( !$obRecordSet->retornaValoresRecordSet( $arObDado->getOcultaComponente() ) ) {
                            if ($arObDado->obLabel->getValue()) {
                                $arObDado->obLabel->setValue( $obRecordSet->retornaValoresRecordSet( $arObDado->obLabel->getValue()) );
                            }

                            /// define se o componente exibido habilitado ou não
                            if (  $arObDado->getDesabilitaComponente () ) {
                                 $arObDado->obComponenteLista->setDisabled ( $obRecordSet->retornaValoresRecordSet( $arObDado->getDesabilitaComponente () ) );
                            }

                            //ALTERARCAO PARA ACEITAR NOMES DOS CAMPOS CONCATENADOS COM CAMPOS DO RS
                            if ( strtolower( $arObDado->obComponenteLista->stDefinicao ) != 'buscainner' ) {
                                $stNomComponenteLista = $arObDado->obComponenteLista->getName();
                            } else {
                                // alteração para poder modificar o nome e id dos dois campos da buscainner
                                $stNomComponenteLista = $arObDado->obComponenteLista->obCampoCod->getName();
                                $stNameOld =  $arObDado->obComponenteLista->obCampoCod->getName();

                                $stNomComponenteDescricaoLista = $arObDado->obComponenteLista->getName();
                                $stNameDescricaoOld = $arObDado->obComponenteLista->getName();

                                $stIdComponenteLista = $arObDado->obComponenteLista->obCampoCod->getId();
                                $stIdOld =  $arObDado->obComponenteLista->obCampoCod->getId();

                            }

                            if (strstr($stNomComponenteLista,'[') || strstr($stNomComponenteLista,']')) {
                                $stNomeCampoComposto = "";
                                $stCampo = $stNomComponenteLista;
                                for ($inCount=0; $inCount<strlen($stCampo); $inCount++) {
                                    if ($stCampo[ $inCount ] == '[') $inInicialId = $inCount;
                                    if (($stCampo[ $inCount ] == ']') && isset($inInicialId) ) {
                                         $stNomeCampoComposto .= $obRecordSet->getCampo( trim( substr($stCampo,$inInicialId+1,(($inCount-$inInicialId)-1)) ) );
                                        unset($inInicialId);
                                    } elseif ( !isset($inInicialId) ) {
                                        $stNomeCampoComposto .= $stCampo[ $inCount ];
                                    }
                                }

                                if ( strtolower( $arObDado->obComponenteLista->stDefinicao ) != 'buscainner' ) {
                                    $arObDado->obComponenteLista->setName( $stNomeCampoComposto );
                                } else {
                                    $arObDado->obComponenteLista->obCampoCod->setName( $stNomeCampoComposto );
                                }
                            }

                            // alteracao para montar o id do campoCod e do campo de descricao
                            if ( strtolower( $arObDado->obComponenteLista->stDefinicao ) == 'buscainner' ) {

                                // ID
                                if (strstr($stIdComponenteLista,'[') || strstr($stIdComponenteLista,']')) {
                                    $stIdCampoComposto = "";
                                    $stIdCampo = $stIdComponenteLista;
                                    for ($inCount=0; $inCount<strlen($stIdCampo); $inCount++) {
                                        if ($stIdCampo[ $inCount ] == '[') $inInicialId = $inCount;
                                        if (($stIdCampo[ $inCount ] == ']') && isset($inInicialId) ) {
                                             $stIdCampoComposto .= $obRecordSet->getCampo( trim( substr($stIdCampo,$inInicialId+1,(($inCount-$inInicialId)-1)) ) );
                                            unset($inInicialId);
                                        } elseif ( !isset($inInicialId) ) {
                                            $stIdCampoComposto .= $stIdCampo[ $inCount ];
                                        }
                                    }
                                    $arObDado->obComponenteLista->obCampoCod->setId( $stIdCampoComposto );
                                }

                                // campo descricao
                                if (strstr($stNomComponenteDescricaoLista,'[') || strstr($stNomComponenteDescricaoLista,']')) {
                                    $stNomeCampoDescricaoComposto = "";
                                    $stCampoDescricao = $stNomComponenteDescricaoLista;
                                    for ($inCount=0; $inCount<strlen($stCampoDescricao); $inCount++) {
                                        if ($stCampoDescricao[ $inCount ] == '[') $inInicialId = $inCount;
                                        if (($stCampoDescricao[ $inCount ] == ']') && isset($inInicialId) ) {
                                             $stNomeCampoDescricaoComposto .= $obRecordSet->getCampo( trim( substr($stCampoDescricao,$inInicialId+1,(($inCount-$inInicialId)-1)) ) );
                                            unset($inInicialId);
                                        } elseif ( !isset($inInicialId) ) {
                                            $stNomeCampoDescricaoComposto .= $stCampoDescricao[ $inCount ];
                                        }
                                    }
                                    $arObDado->obComponenteLista->setName( $stNomeCampoComposto );
                                }
                            }

                            if ( $arObDado->getNameSequencial() ) {
                                if ( strstr($arObDado->obComponenteLista->getName(),"_") ) {
                                    $stName  = substr( $arObDado->obComponenteLista->getName(),0,strrpos($arObDado->obComponenteLista->getName(),"_")+1 );
                                    $stName .= $inContador;
                                } else {
                                    $stName  = $arObDado->obComponenteLista->getName() . "_";
                                    $stName .= $inContador;
                                }
                                $arObDado->obComponenteLista->setName( $stName );
                                $arObDado->obComponenteLista->setId( $stName );
                            }
                            $arObDado->obComponenteLista->setValue( $obRecordSet->retornaValoresRecordSet( $arObDado->obComponenteLista->getValue()) );

                            if ( strtolower(get_class($arObDado->obComponenteLista))=='checkbox' ) {
                                $arObDado->obComponenteLista->setChecked( trim($obRecordSet->retornaValoresRecordSet( $arObDado->getCampo())) );
                            } elseif ( strtolower(get_class($arObDado->obComponenteLista))=='radio') {
                                $arObDado->obComponenteLista->setChecked( trim($obRecordSet->retornaValoresRecordSet( $arObDado->getCampo())) );
                            } elseif ( strtolower( $arObDado->obComponenteLista->stDefinicao )=='buscainner' ) {
                                //Estavam ocorrendo problemas com referência dos objetos. O código abaixo solucionou o problema.
                                if ( strstr($arObDado->obComponenteLista->obCampoCod->getName(),"_") ) {
                                    $stName  = substr( $arObDado->obComponenteLista->obCampoCod->getName(),0,strrpos($arObDado->obComponenteLista->obCampoCod->getName(),"_")+1 );
                                    $stName .= $inContador;
                                } else {
                                    $stName  = $arObDado->obComponenteLista->obCampoCod->getName() . "_";
                                    $stName .= $inContador;
                                }
                                $arObDado->obComponenteLista->obCampoCod->setName( $stName );

                                if ( strstr($arObDado->obComponenteLista->obCampoCod->getId(),"_") ) {
                                    $stId  = substr( $arObDado->obComponenteLista->obCampoCod->getId(),0,strrpos($arObDado->obComponenteLista->obCampoCod->getId(),"_")+1 );
                                    $stId .= $inContador;
                                } else {
                                    $stId  = $arObDado->obComponenteLista->obCampoCod->getId() . "_";
                                    $stId .= $inContador;
                                }
                                $arObDado->obComponenteLista->obCampoCod->setId( $stId );

                                $stValue  = substr( $arObDado->obComponenteLista->obCampoCod->getName(),0,strpos($arObDado->obComponenteLista->obCampoCod->getName(),"_") );
                                $$stValue = ($$stValue=='') ? $arObDado->obComponenteLista->obCampoCod->getValue() : $$stValue;
                                $arObDado->obComponenteLista->obCampoCod->setValue( $obRecordSet->retornaValoresRecordSet( $$stValue) );

                                # 20965 - Ao usar o componente em uma lista, não estava retornando o campo texto devido ao id ser sempre o mesmo.
                                # Essa rotina adiciona um sequencial para indicar qual campo deve receber o valor.
                                if ( strstr($arObDado->obComponenteLista->getName(),"_") ) {
                                    $stDescName  = substr( $arObDado->obComponenteLista->getName(),0,strrpos($arObDado->obComponenteLista->getName(),"_")+1 );
                                    $stDescName .= $inContador;
                                } else {
                                    $stDescName  = $arObDado->obComponenteLista->getName() . "_";
                                    $stDescName .= $inContador;
                                }

                                $arObDado->obComponenteLista->setFuncaoBusca( str_replace("'".$stValue."'", "'".$stName."'", $arObDado->obComponenteLista->getFuncaoBusca()) );
                                $arObDado->obComponenteLista->setFuncaoBusca( str_replace("'".$stNameDescricaoOld."'", "'".$stDescName."'", $arObDado->obComponenteLista->getFuncaoBusca()) );

                            } elseif ( strtolower(get_class($arObDado->obComponenteLista))=='select' ) {
                                if ( strstr($arObDado->obComponenteLista->getId(),"_") ) {
                                    $stId  = substr( $arObDado->obComponenteLista->getId(),0,strrpos($arObDado->obComponenteLista->getId(),"_")+1 );
                                    $stId .= $inContador;
                                } else {
                                    $stId  = $arObDado->obComponenteLista->getId() . "_";
                                    $stId .= $inContador;
                                }
                                $arObDado->obComponenteLista->setId( $obRecordSet->retornaValoresRecordSet( $stId ) );

                            }
                            $arObDado->addConteudo( $stConteudo );
                            $this->ultimaLinha->addCelula();
                            $arObDado->obComponenteLista->montaHTML();
                            if ( strtolower( $arObDado->obComponenteLista->stDefinicao )=='buscainner' ) {
                                $arObDado->obComponenteLista->obCampoCod->setName( $stNameOld );
                                $arObDado->obComponenteLista->setName( $stNameDescricaoOld );
                                $arObDado->obComponenteLista->obCampoCod->setId( $stIdOld );
                            }

                            $stClass = ( $arObDado->getClass() ) ? $arObDado->getClass() : "show_dados";
                            $this->ultimaLinha->ultimaCelula->setClass( $stClass );
                            $this->ultimaLinha->ultimaCelula->addConteudo( $arObDado->obComponenteLista->getHTML() );
                            $this->ultimaLinha->commitCelula();

                            if ($inCountAlternado == 1) {
                                $this->ultimaLinha->ultimaCelula->setStyle ("background:#edf4fa;");
                            } else {
                                if ($this->getAlternado()) {
                                   $this->ultimaLinha->ultimaCelula->setStyle ("background:#d0e4f2;");

                                } else {
                                   $this->ultimaLinha->ultimaCelula->setStyle ("background:#edf4fa");

                                }
                            }

                        } else {
                            $this->ultimaLinha->addCelula();
                            $this->ultimaLinha->ultimaCelula->setClass( "show_dados" );
                            $this->ultimaLinha->ultimaCelula->addConteudo( '&nbsp;' );
                            $this->ultimaLinha->commitCelula();
                        }
                    } else {
                        if (!(is_null($stConteudo))) {
                            $arObDado->addConteudo( str_replace( "  "," &nbsp;", $stConteudo ) );
                        } else {
                            $arObDado->addConteudo( "&nbsp;" );
                        }
                        $this->ultimaLinha->setUltimaCelula($arObDado);
                        $this->ultimaLinha->commitCelula();
                    }
                }
                $stAcao = "";
                if ( count( $arAcao ) ) {
                    foreach ($arAcao as $obAcao) {
                        $obAcao = clone $obAcao;

                        $arLink = $obAcao->getCampo();
                        $stLink = "";
                        if ( is_array( $arLink ) ) {
                            if ( !$obAcao->getFuncao() ) {
                                foreach ($arLink as $stIndice => $stCampo) {
                                    //ALTERARCAO PARA ACEITAR CHAVE COMPOSTA NA LISTA
                                    if (strstr($stCampo,'[') || strstr($stCampo,']')) {
                                        $stLinkComposto = "";
                                        for ($inCount=0; $inCount<strlen($stCampo); $inCount++) {
                                            if ($stCampo[ $inCount ] == '[') $inInicialId = $inCount;
                                            if (($stCampo[ $inCount ] == ']') && isset($inInicialId) ) {
                                                 $stLinkComposto .= urlencode($obRecordSet->getCampo( trim( substr($stCampo,$inInicialId+1,(($inCount-$inInicialId)-1)) ) ));
                                                unset($inInicialId);
                                            } elseif ( !isset($inInicialId) ) {
                                                $stLinkComposto .= urlencode($stCampo[ $inCount ]);
                                            }
                                        }
                                        $stLink .= $stIndice."=".addslashes($stLinkComposto)."&";
                                    } else {
                                        $stLink .= $stIndice."=".urlencode(addslashes($obRecordSet->getCampo( $stCampo )))."&";
                                    }
                                }
                                $stLink = substr( $stLink , 0, strlen($stLink) - 1 );
                                $stLink = $obAcao->getLink().$stLink;
                            } else {
                                foreach ($arLink as $stIndice => $stCampo) {
                                    //ALTERARCAO PARA ACEITAR CHAVE COMPOSTA NAS FUNÇÕES DA LISTA
                                    if (strstr($stCampo,'[') || strstr($stCampo,']')) {
                                        $stLinkComposto = "";
                                        for ($inCount=0; $inCount<strlen($stCampo); $inCount++) {
                                            if ($stCampo[ $inCount ] == '[') $inInicialId = $inCount;
                                            if (($stCampo[ $inCount ] == ']') && isset($inInicialId) ) {
                                                 $stLinkComposto .= urlencode($obRecordSet->getCampo( trim( substr($stCampo,$inInicialId+1,(($inCount-$inInicialId)-1)) ) ));
                                                unset($inInicialId);
                                            } elseif ( !isset($inInicialId) ) {
                                                $stLinkComposto .= urlencode($stCampo[ $inCount ]);
                                            }
                                        }
                                        $stLink .= "'".addslashes($stLinkComposto)."', ";
                                    } else {
                                        $stLink .= "'".addslashes($obRecordSet->getCampo( $stCampo ))."', ";
                                    }
                                }
                                $stLink = substr( $stLink , 0, strlen($stLink) - 2 );
                                $inPosLink = strrpos( $obAcao->getLink(), ")" );
                                $stFncAcao = trim( substr( $obAcao->getLink(), 0, $inPosLink ) );
                                if ( strlen( $stFncAcao ) != strrpos( $stFncAcao, "(" ) + 1 ) {
                                    $stFncAcao .= ", ";
                                }
                                $stLink = $stFncAcao.$stLink.");";
                            }
                            if ( $obAcao->getFuncaoAjax() ) {
                                $stLink = "";
                                foreach ($arLink as $stIndice => $stCampo) {
                                    $stLink .= "&".$stCampo."=". addslashes($obRecordSet->getCampo( $stCampo ) );
                                }
                                $inPosLink = strrpos( $obAcao->getLink(), ")" );
                                $stFncAcao = trim( substr( $obAcao->getLink(), 0, $inPosLink ) );
                                $stLink = $stFncAcao.",'".$stLink."');";
                            }
                        }
                        $obAcao->setLink( $stLink );
                        if ( strstr($obAcao->getAcao(),"_") ) {
                            $arPartesAcao = explode( "_" , $obAcao->getAcao() );
                            $obAcao->setAcao( $arPartesAcao[0] );
                        }
                        switch ( $obAcao->getAcao() ) {
                            case 'excluir':
                                $obAcao->setLink( str_replace('&','*_*',$obAcao->getLink()) );
                                $obAcao->setLink( "javascript:alertaQuestao('".$obAcao->getLink()."','sn_excluir','".Sessao::getId()."'); BloqueiaFrames(true,false);" );
                            break;
                            case 'remover':
                                $obAcao->setLink( str_replace('&','*_*',$obAcao->getLink()) );
                                $obAcao->setLink( "javascript:alertaQuestaoPopUp('".$obAcao->getLink()."','pp_excluir','".Sessao::getId()."');" );
                            break;
                            case 'popup':
                                $obAcao->setLink( str_replace('&','*_*',$obAcao->getLink()) );
                                $obAcao->setLink( "javascript:alertaQuestao('".$obAcao->getLink()."','sn_excluir','".Sessao::getId()."');" );
                            break;
                            case 'Cancelar':
                                $obAcao->setLink( str_replace('&','*_*',$obAcao->getLink()) );
                                $obAcao->setLink( "javascript:alertaQuestao('".$obAcao->getLink()."','sn_cancelar','".Sessao::getId()."');" );
                            break;
                            case 'imprimir':
                                $obAcao->setLink( "javascript:var x = window.open('".$obAcao->getLink()."', '".(($obAcao->getTarget()) ? $obAcao->getTarget() : 'oculto')."');" );
                            break;
                            case 'reemitir':
                                $obAcao->setLink( "javascript:var x = window.open('".$obAcao->getLink()."','oculto');" );
                            break;
                            case 'liquidar': // Favor verificar esta linha caso ocorra algum problema com outras ações que usam o nome Liquidar,
                                             // pois podem acabar congelando a tela e mostrando a imagem carregando, caso seja um incômodo.
                                $obAcao->setLink( "javascript:var x = mudaTelaPrincipal('".$obAcao->getLink()."');BloqueiaFrames(true,false);" );
                            break;
                            case 'alterar':
                                if (isset($_SERVER["HTTP_REFERER"])) {
                                    $stHttpReferer = $_SERVER["HTTP_REFERER"];
                                } else {
                                    $stHttpReferer = null;
                                }
                                $stReferer = substr(basename($stHttpReferer),0,strpos(basename($stHttpReferer),'.php'));
                                $stProxima = substr(basename($stLink),0,strpos(basename($stLink),'.php'));
                                if ( $this->boEncRegistroUnico and $stReferer != $stProxima and strpos(basename($stHttpReferer),'PR') !== 0) {
                                    if ( $obRecordSet->getNumLinhas() == 1 and count($arAcao) == 1 and !$obAcao->getFuncao() ) {
                                            print '<script type="text/javascript">
                                                   mudaTelaPrincipal("'.$stLink.'");
                                                   </script>';
                                    }
                                }
                            break;
                        }
                        $obAcao->setLinkId( $obAcao->getLinkId()."_".$inContador );
                        $obAcao->montaHTML();
                        $stAcao .= $obAcao->getHTML()."&nbsp;";
                    }
                    if ( $obAcao->getUnicoBotao() ) {
                        $stAcao = substr($stAcao,0, strlen($stAcao) - 6 );
                        if ( $obAcao->getAcaoUnicoBotao() != "ultimo" and $inContador == 1 ) {
                            $this->ultimaLinha->addCelula();
                            $this->ultimaLinha->ultimaCelula->setClass( "botao" );
                            $this->ultimaLinha->ultimaCelula->setRowSpan($obRecordSet->getNumLinhas());
                            $this->ultimaLinha->ultimaCelula->addConteudo( $stAcao );
                            $this->ultimaLinha->commitCelula();
                        } elseif ($obAcao->getAcaoUnicoBotao() == "ultimo") {
                            $this->ultimaLinha->addCelula();
                            $this->ultimaLinha->ultimaCelula->setClass( "botao" );
                            if ( $inContador == $obRecordSet->getNumLinhas() ) {
                                $this->ultimaLinha->ultimaCelula->addConteudo( $stAcao );
                            } else {
                                $this->ultimaLinha->ultimaCelula->addConteudo( "" );
                            }
                            $this->ultimaLinha->commitCelula();
                        }
                    } else {
                        $stAcao = substr($stAcao,0, strlen($stAcao) - 6 );
                        $this->ultimaLinha->addCelula();
                        if ($this->getAlternado()) {
                            if ($inCountAlternado == 1) {
                                $this->ultimaLinha->ultimaCelula->setClass( "botao" );
                            } else {
                                $this->ultimaLinha->ultimaCelula->setClass( "botaoEscuro" );
                            }
                        } else {
                            $this->ultimaLinha->ultimaCelula->setClass( "botao" );
                        }

                        $obAcao->montaHTML();
                        $this->ultimaLinha->ultimaCelula->addConteudo( $stAcao );
                        $this->ultimaLinha->commitCelula();
                    }
                }
                $this->commitLinha();
            }
            $obRecordSet->proximo();
            $inContador++;

            if ($this->getMostraScroll())
                $this->fechaDiv();
        }

        // mostra totalizador
        if ( $this->getTotaliza() && !$this->getMostraPaginacao()) {
            // adiciona mais 1 no contador de celulas por causa do contador de linha
            // adiciona unm de ações
            $inNumCel = ++$inNumCel + count($this->arAcao);

            // array separado por virgula
            // campo, nome_totalizado, alinhamento , posição
            $arOpcoes = explode(",", $this->getTotaliza());
            // tratar opções de alinhamento
            switch (strtolower($arOpcoes[2])) {
                case "r" :
                case "right" :
                case "direita" :
                    $arOpcoes[2] = "right";
                    break;
                case "l" :
                case "left" :
                case "esquerda" :
                    $arOpcoes[2] = "left";
                    break;
                case "c" :
                case "center" :
                case "centro" :
                    $arOpcoes[2] = "center";
                    break;
                default: $arOpcoes[2] = "right";
            }
            // retorna objeto recordset
            $obRecordSet->setPrimeiroElemento();
            // faz loop no recordset e soma campo informado
            $nuSoma = 0.00;
            while ( !$obRecordSet->eof()) {
                $nuValorCampo = str_replace(".","",$obRecordSet->getCampo($arOpcoes[0]));
                $nuValorCampo = str_replace(",",".",$nuValorCampo);
                $nuSoma = $nuSoma + $nuValorCampo;
                $obRecordSet->proximo();
            }

            // --  aplicar mesma formatação do campo na totalização
            // pegar formatacao
            if ($obRecordSet->arFormatacao[$arOpcoes[0]]) {
                $stFormatacao = $obRecordSet->arFormatacao[$arOpcoes[0]];
            } else {
                $stFormatacao = "NUMERIC_BR";
            }
            // usa recordset temporario para formatar valor de soma
            $arValor[] = array("valor" => $nuSoma);
            $rsTemp = new Recordset;
            $rsTemp->preenche($arValor);
            $rsTemp->addFormatacao("valor",$stFormatacao);
            $rsTemp->setPrimeiroElemento();
            $nuSoma = $rsTemp->getCampo("valor");

            $this->addLinha();
            $this->ultimaLinha->addCelula();
            $this->ultimaLinha->ultimaCelula->setColSpan ( $arOpcoes[3] -1);
            $this->ultimaLinha->ultimaCelula->setClass   ( "label" );
            $this->ultimaLinha->ultimaCelula->addConteudo( $arOpcoes[1] );
            $this->ultimaLinha->commitCelula();
            $this->ultimaLinha->addCelula();
            $this->ultimaLinha->ultimaCelula->setClass   ( "show_dados_".$arOpcoes[2]);
            $this->ultimaLinha->ultimaCelula->addConteudo( "<b>".$nuSoma."</b>" );
            $this->ultimaLinha->commitCelula();
            
            if ($inNumDados > $arOpcoes[3]) {
                $this->ultimaLinha->addCelula();
                $this->ultimaLinha->ultimaCelula->setColSpan ( $inNumCel - $arOpcoes[3] );
                $this->ultimaLinha->ultimaCelula->setClass   ( "label"  );
                $this->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
                $this->ultimaLinha->commitCelula();
            }

            $this->commitLinha();
        } // fim mostra totalizador

        // mostra totalizador multiplo

        // primeiro seta um array com o método setTotalizaMultiplo( $arValor ), onde cada posição do array é composto de:
        // campo(campo do recordset), nome_totalizado(nome do label para a coluna),
        // alinhamento(alinhamento da coluna para o valor do recordset) , posição(coluna)
        // retorna o total de itens de determinado campo( em números inteiros )
        if ( count( $this->getTotalizaMultiplo() ) > 0 && !$this->getMostraPaginacao()) {
            foreach ( $this->getTotalizaMultiplo() as $stValor ) {
                // adiciona mais 1 no contador de celulas por causa do contador de linha
                // adiciona unm de ações
                $inNumCel = ++$inNumCel + count($this->arAcao);

                // array separado por virgula
                // campo, nome_totalizado, alinhamento , posição
                $arOpcoes = explode(",", $stValor);

                // tratar opções de alinhamento
                switch (strtolower($arOpcoes[2])) {
                    case "r" :
                    case "right" :
                    case "direita" :
                        $arOpcoes[2] = "right";
                        break;
                    case "l" :
                    case "left" :
                    case "esquerda" :
                        $arOpcoes[2] = "left";
                        break;
                    case "c" :
                    case "center" :
                    case "centro" :
                        $arOpcoes[2] = "center";
                        break;
                    default: $arOpcoes[2] = "right";
                }

                $arNome[] = $arOpcoes[1];
                $arFormatacao[] = $arOpcoes[2];
                $arColuna[] = $arOpcoes[3];

                // retorna objeto recordset
                $obRecordSet->setPrimeiroElemento();
                // faz loop no recordset e soma campo informado
                $nuSoma = 0.00;
                while ( !$obRecordSet->eof()) {
                    $nuValorCampo = str_replace(".","",$obRecordSet->getCampo($arOpcoes[0]));
                    $nuValorCampo = str_replace(",",".",$nuValorCampo);
                    $nuSoma = $nuSoma + $nuValorCampo;
                    $obRecordSet->proximo();
                }

                // --  aplicar mesma formatação do campo na totalização
                // pegar formatacao
                if ($obRecordSet->arFormatacao[$arOpcoes[0]]) {
                    $stFormatacao = $obRecordSet->arFormatacao[$arOpcoes[0]];
                } else {
                    $stFormatacao = "NUMERIC_BR";
                }
                // usa recordset temporario para formatar valor de soma
                $arValor[] = array("valor" => $nuSoma);
            }

            $boEntrou = false;
            $inNumCelulas = 0;

            $this->addLinha();
            for ( $cont = 0; $cont <= count($arNome); $cont++ ) {
                for ($i = 0; $i < end($arColuna); $i++) {
                    if ($i ==  $arColuna[$cont]) {
                        $this->ultimaLinha->addCelula();
                        $this->ultimaLinha->ultimaCelula->setClass   ( "labelcenter" );
                        $this->ultimaLinha->ultimaCelula->addConteudo( $arNome[$cont] );
                        $this->ultimaLinha->commitCelula();

                        $boEntrou = true;
                        $inNumCelulas++;
                    } elseif (!$boEntrou) {
                        $this->ultimaLinha->addCelula();
                        $this->ultimaLinha->ultimaCelula->setClass   ( "label" );
                        $this->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
                        $this->ultimaLinha->commitCelula();

                        $inNumCelulas++;
                    }
                }
            }

            if ( count($this->getCabecalho()) > $inNumCelulas ) {
                for ( $i = $inNumCelulas; $i < count($this->getCabecalho()); $i++ ) {
                    $this->ultimaLinha->addCelula();
                    $this->ultimaLinha->ultimaCelula->setClass   ( "label"  );
                    $this->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
                    $this->ultimaLinha->commitCelula();
                }
            }
            $this->commitLinha();

            $rsTemp = new Recordset;
            $rsTemp->preenche($arValor);
            $rsTemp->addFormatacao("valor",$stFormatacao);
            $rsTemp->setPrimeiroElemento();

            $cont = 0;
            $boEntrou = false;
            $inNumCelulas = 0;

            $this->addLinha();
            while ( !$rsTemp->eof() ) {
                $nuSoma = $rsTemp->getCampo("valor");
                for ($i = 0; $i <= $arColuna[$cont]; $i++) {
                    if ($i ==  $arColuna[$cont]) {
                        $this->ultimaLinha->addCelula();
                        $this->ultimaLinha->ultimaCelula->setClass   ( "show_dados_".$arFormatacao[$cont]);
                        $this->ultimaLinha->ultimaCelula->addConteudo( $nuSoma);
                        $this->ultimaLinha->commitCelula();

                        $boEntrou = true;
                        $inNumCelulas++;
                    } elseif (!$boEntrou) {
                        $this->ultimaLinha->addCelula();
                        $this->ultimaLinha->ultimaCelula->setClass   ( "show_dados_".$arFormatacao[$cont] );
                        $this->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
                        $this->ultimaLinha->commitCelula();

                         $inNumCelulas++;
                    }
                }
                $rsTemp->proximo();
                $cont++;
            }

            if ( count($this->getCabecalho()) > $inNumCelulas ) {
                for ( $i = $inNumCelulas; $i < count($this->getCabecalho()); $i++ ) {
                    $this->ultimaLinha->addCelula();
                    $this->ultimaLinha->ultimaCelula->setClass   ( "show_dados_".$arFormatacao[0] );
                    $this->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
                    $this->ultimaLinha->commitCelula();
                }
            }
            $this->commitLinha();

        }//fim totalizar multiplo

        ##################################
        #Somatório Versão 2
        ##################################
        if ($boSomatorio) {
            $this->addLinha();
            $this->ultimaLinha->addCelula();
            $this->ultimaLinha->ultimaCelula->setColSpan ( count($this->arCabecalho) );
            $this->ultimaLinha->ultimaCelula->setClass   ( "labelleft" );
            $this->ultimaLinha->ultimaCelula->addConteudo( $this->getRotuloSomatorio() );
            $this->ultimaLinha->commitCelula();
            $this->commitLinha();

            $this->addLinha();
            $this->ultimaLinha->addCelula();
            $this->ultimaLinha->ultimaCelula->setClass   ( "label" );
            $this->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
            $this->ultimaLinha->commitCelula();
            foreach ($arTotalizar as $arValor) {
                if ($arValor[1] == "NUMERIC_BR") {
                    $inValor = number_format($arValor[0],2,',','.');
                } else {
                    $inValor = $arValor[0];
                }
                $this->ultimaLinha->addCelula();
                $this->ultimaLinha->ultimaCelula->setClass   ( "show_dados_right" );
                $this->ultimaLinha->ultimaCelula->addConteudo( $inValor );
                $this->ultimaLinha->commitCelula();
            }
            if (count( $arAcao )) {
                $this->ultimaLinha->addCelula();
                $this->ultimaLinha->ultimaCelula->setClass   ( "label" );
                $this->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
                $this->ultimaLinha->commitCelula();
            }
            $this->commitLinha();
        }
        ##################################
        #Fim Somatório Versão 2
        ##################################
        parent::montaHTML();
        $stHTML = $this->getHTML();

        // VERIFICA SE E OU NAO PARA EXIBIR O CHECKBOX PARA SELECIONAR TODOS AS CHECKBOX DA LISTA //
        if ( $this->getMostraSelecionaTodos() ) {

            $obCheckbox = new Checkbox;
            $obCheckbox->setName  ( "boTodos" );
            $obCheckbox->setId    ( "boTodos" );
            $obCheckbox->setLabel( "Selecionar todos" );
            $obCheckbox->obEvento->setOnChange( "javascript:selecionarTodos()" );
            $obCheckbox->montaHTML();

            $obTabelaCheckbox = new Tabela;
            $obTabelaCheckbox->addLinha();
            $obTabelaCheckbox->ultimaLinha->addCelula();
            $obTabelaCheckbox->ultimaLinha->ultimaCelula->setColSpan ( $inNumDados + 2 );
            $obTabelaCheckbox->ultimaLinha->ultimaCelula->setClass   ( $this->getClassPaginacao() );
            $obTabelaCheckbox->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>".$obCheckbox->getHTML()."&nbsp;</div>" );
            $obTabelaCheckbox->ultimaLinha->commitCelula();
            $obTabelaCheckbox->commitLinha();
            $obTabelaCheckbox->montaHTML();
            $stHTML .= $obTabelaCheckbox->getHTML();
        }

        if ( $this->getMostraPaginacao() ) {
            $this->obPaginacao->montaHTML();
            $obTabelaPaginacao = new Tabela;
            $obTabelaPaginacao->addLinha();
            $obTabelaPaginacao->ultimaLinha->addCelula();
            $obTabelaPaginacao->ultimaLinha->ultimaCelula->setClass( $this->getClassPaginacao() );
            $obTabelaPaginacao->ultimaLinha->ultimaCelula->setColSpan( $inNumDados + 2  );
            $obTabelaPaginacao->ultimaLinha->ultimaCelula->addConteudo("<font size='2'>".$this->obPaginacao->getHTML()."</font>" );
            $obTabelaPaginacao->ultimaLinha->commitCelula();
            $obTabelaPaginacao->commitLinha();
            $obTabelaPaginacao->addLinha();
            $obTabelaPaginacao->ultimaLinha->addCelula();
            $obTabelaPaginacao->ultimaLinha->ultimaCelula->setClass( $this->getClassPaginacao() );
            $obTabelaPaginacao->ultimaLinha->ultimaCelula->setColSpan( $inNumDados + 2  );
            $obTabelaPaginacao->ultimaLinha->ultimaCelula->addConteudo("<font size='2'>Registros encontrados: ".$this->obPaginacao->getNumeroLinhas()."</font>" );
            $obTabelaPaginacao->ultimaLinha->commitCelula();
            $obTabelaPaginacao->commitLinha();
            $obTabelaPaginacao->montaHTML();
            $stHTML .= $obTabelaPaginacao->getHTML();
        }

    } else {
        $onNunhumRegistro = new Dado;
        $onNunhumRegistro->setAlinhamento( "CENTER" );
        $onNunhumRegistro->addConteudo( "Nenhum registro encontrado!" );
        $onNunhumRegistro->setColSpan( $inNumDados + 2 );
        $this->addLInha();
        $this->ultimaLinha->setUltimaCelula( $onNunhumRegistro );
        $this->ultimaLinha->commitCelula();
        $this->commitLinha();
        parent::montaHTML();
        $stHTML = $this->getHTML();
    }
    $obHndNumLinhas = new Hidden();
    $obHndNumLinhas->setId('hdn'.$this->getId().'NumLinhas');
    $obHndNumLinhas->setValue($obRecordSet->getNumLinhas());
    $obHndNumLinhas->montaHTML();
    $stHTML = $stHTML.$obHndNumLinhas->getHTML();
    $this->setHTML( $stHTML );
}

function montaInnerHtml()
{
    $this->montaHTML();
    $stHtml = str_replace("\n","",$this->getHTML());
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stHtml = str_replace(chr(13),"",$stHtml);
    $stHtml = str_replace(chr(13).chr(10),"",$stHtml);
    $this->setHTML( $stHtml );
}

}
?>
