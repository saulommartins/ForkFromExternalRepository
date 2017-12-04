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
    * Classe de Regra do Relatório de Resumo de Receita
    * Data de Criação   : 15/12/2005

    * @author Analista: Lucas Leusin Oiagem
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Regra

    $Revision: 30835 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.16
*/

/*
$Log$
Revision 1.8  2007/08/10 18:35:41  cako
Bug#9842#

Revision 1.7  2007/07/31 14:12:21  vitor
Bug#9783#

Revision 1.6  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO   );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"             );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

/**
    * Classe de Regra de Negócios Transferencias Bancarias
    * @author Desenvolvedor: Jose Eduardo Porto
*/
class RTesourariaRelatorioResumoReceita extends PersistenteRelatorio
{
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stEntidade;
/**
    * @var String
    * @access Private
*/
var $stDataInicial;
/**
    * @var String
    * @access Private
*/
var $stDataFinal;
/**
    * @var String
    * @access Private
*/
var $stFiltro;
/**
    * @var Integer
    * @access Private
*/
var $inReceitaInicial;
/**
    * @var Integer
    * @access Private
*/
var $inReceitaFinal;
/**
    * @var Integer
    * @access Private
*/
var $inContaBancoInicial;
/**
    * @var Integer
    * @access Private
*/
var $inContaBancoFinal;
/**
    * @var String
    * @access Private
*/
var $stTipoRelatorio;
/**
    * @var Integer
    * @access Private
*/
var $inCodRecurso;

var $stTipoReceita;

var $stDestinacaoRecurso;
var $inCodDetalhamento;

/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setEntidade($valor) { $this->stEntidade           = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataInicial($valor) { $this->stDataInicial      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataFinal($valor) { $this->stDataFinal      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setFiltro($valor) { $this->stFiltro      = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setReceitaInicial($valor) { $this->inReceitaInicial= $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setReceitaFinal($valor) { $this->inReceitaFinal= $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setContaBancoInicial($valor) { $this->inContaBancoInicial= $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setContaBancoFinal($valor) { $this->inContaBancoFinal= $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setTipoRelatorio($valor) { $this->stTipoRelatorio      = $valor; }
function setTipoReceita($valor) { $this->stTipoReceita        = $valor; }

function setDestinacaoRecurso($valor) { $this->stDestinacaoRecurso = $valor; }
function setCodDetalhamento($valor) { $this->inCodDetalhamento = $valor; }

/**
     * @access Public
     * @param Integer $valor
*/
function setCodRecurso($valor) { $this->inCodRecurso= $valor; }
/*
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;                      }
/*
    * @access Public
    * @return String
*/
function getEntidade() { return $this->stEntidade;                      }
/*
    * @access Public
    * @return String
*/
function getDataInicial() { return $this->stDataInicial;                      }
/*
    * @access Public
    * @return String
*/
function getDataFinal() { return $this->stDataFinal;                      }
/*
    * @access Public
    * @return String
*/
function getFiltro() { return $this->stFiltro;                      }
/*
    * @access Public
    * @return Integer
*/
function getReceitaInicial() { return $this->inReceitaInicial;   }
/*
    * @access Public
    * @return Integer
*/
function getReceitaFinal() { return $this->inReceitaFinal;                      }
/*
    * @access Public
    * @return Integer
*/
function getContaBancoInicial() { return $this->inContaBancoInicial;   }
/*
    * @access Public
    * @return Integer
*/
function getContaBancoFinal() { return $this->inContaBancoFinal;                      }
/*
    * @access Public
    * @return String
*/
function getTipoRelatorio() { return $this->stTipoRelatorio;                      }
function getTipoReceita() { return $this->stTipoReceita;                        }
/*
    * @access Public
    * @return Integer
*/
function getCodRecurso() { return $this->inCodRecurso;                      }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaRelatorioResumoReceita()
{
    $this->obRTesourariaBoletim            = new RTesourariaBoletim;
    $this->obRRelatorio                    = new RRelatorio;
    $this->obRTesourariaBoletim->addArrecadacao();
    $this->obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );

}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    include_once( CAM_GF_TES_MAPEAMENTO."FTesourariaResumoReceita.class.php");
    $obFTesourariaResumoReceita = new FTesourariaResumoReceita;

    $obFTesourariaResumoReceita->setDado("stEntidade"           ,$this->getEntidade());
    $obFTesourariaResumoReceita->setDado("stExercicio"          ,$this->getExercicio());
    $obFTesourariaResumoReceita->setDado("stDataInicial"        ,$this->getDataInicial());
    $obFTesourariaResumoReceita->setDado("stDataFinal"          ,$this->getDataFinal());
    $obFTesourariaResumoReceita->setDado("stTipoRelatorio"      ,$this->getTipoRelatorio());
    $obFTesourariaResumoReceita->setDado("inReceitaInicial"     ,$this->getReceitaInicial());
    $obFTesourariaResumoReceita->setDado("inReceitaFinal"       ,$this->getReceitaFinal());
    $obFTesourariaResumoReceita->setDado("inContaBancoInicial"  ,$this->getContaBancoInicial());
    $obFTesourariaResumoReceita->setDado("inContaBancoFinal"    ,$this->getContaBancoFinal());
    $obFTesourariaResumoReceita->setDado("inCodRecurso"         ,$this->getCodRecurso());
    $obFTesourariaResumoReceita->setDado("inNumCgm"             ,$this->obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->getNumCGM());
    $obFTesourariaResumoReceita->setDado("stTipoReceita"        ,$this->getTipoReceita());
    $obFTesourariaResumoReceita->setDado("stDestinacaoRecurso"  ,$this->stDestinacaoRecurso );
    $obFTesourariaResumoReceita->setDado("inCodDetalhamento"    ,$this->inCodDetalhamento );
    $obFTesourariaResumoReceita->setDado("boUtilizaEstruturalTCE", 'false' );

    if (Sessao::getExercicio() > '2012' and $this->getTipoReceita() != 'extra') {
        $obFTesourariaResumoReceita->setDado("boUtilizaEstruturalTCE"    , 'true' );
    }

    $stOrder = "ORDER BY CASE WHEN tipo_receita = ''
                            THEN '99999'
                            ELSE tipo_receita
                         END, tipo DESC, receita ";

    $obErro = $obFTesourariaResumoReceita->recuperaTodos( $rsResumoReceita, $stFiltro, $stOrder );

    $arResumoReceita = array();

    if ( $rsResumoReceita->getNumLinhas() > -1 ) {

        switch ( $this->getTipoRelatorio() ) {

            case "B":
                    $label_receita  = "Conta Banco:";
                    $nome_campo     = "Receita";
                    $label_subTotal = "Total Geral das Receitas para esta Conta Banco";
                break;

            case "R":
                    $label_receita  = "Recurso:";
                    $nome_campo     = "Código";
                    $label_subTotal = "Total Geral das Receitas para este Recurso";
                break;

            case "E":
                    $label_receita  = "Entidade:";
                    $nome_campo     = "Código";
                    $label_subTotal = "Total Geral das Receitas por Entidade";
                break;

            default:
                    $nome_campo     = "Receita";
                    $label_subTotal = "Total Geral das Receitas";
        }

        $inCount = 0;

        $subTotalArrecadadoOrcamentario     = 0;

        $subTotalEstornadoOrcamentario      = 0;

        $subTotalArrecadadoExtrOrcamentario = 0;

        $subTotalEstornadoExtraOrcamentario = 0;

        $subTotalArrecadado   = 0;

        $subTotalEstornado    = 0;

        $totalGeralArrecadado = 0;

        $totalGeralEstornado  = 0;

        $tipo_receita = $rsResumoReceita->getCampo("tipo_receita");

        $arResumoReceita[$inCount]["receita"]        = "";
        $arResumoReceita[$inCount]["descricao"]      = "";
        $arResumoReceita[$inCount]["tipo"]           = "";
        $arResumoReceita[$inCount]["arrecadado"]     = "";
        $arResumoReceita[$inCount]["estornado"]      = "";
        $arResumoReceita[$inCount]["total"]          = "";

        if ( $this->getTipoRelatorio() != "" ) {

            $inCount++;
            $arResumoReceita[$inCount]["receita"]      = $label_receita;
            $arResumoReceita[$inCount]["descricao"]    = $rsResumoReceita->getCampo("tipo_receita");
            $arResumoReceita[$inCount]["tipo"]         = "";
            $arResumoReceita[$inCount]["arrecadado"]   = "";
            $arResumoReceita[$inCount]["estornado"]    = "";
            $arResumoReceita[$inCount]["total"]        = "";

            $inCount++;
            $arResumoReceita[$inCount]["receita"]      = $nome_campo;
            $arResumoReceita[$inCount]["descricao"]    = "Descrição";
            $arResumoReceita[$inCount]["tipo"]         = "Tipo";
            $arResumoReceita[$inCount]["arrecadado"]   = "Vlr. Arrec.";
            $arResumoReceita[$inCount]["estornado"]    = "Vlr. Estorn.";
            $arResumoReceita[$inCount]["total"]        = "Total Arrecadado";

            $inCount++;
            $arResumoReceita[$inCount]["receita"]        = "";
        } else {

            $inCount++;
            $arResumoReceita[$inCount]["receita"]      = $nome_campo;
            $arResumoReceita[$inCount]["descricao"]    = "Descrição";
            $arResumoReceita[$inCount]["tipo"]         = "Tipo";
            $arResumoReceita[$inCount]["arrecadado"]   = "Vlr. Arrec.";
            $arResumoReceita[$inCount]["estornado"]    = "Vlr. Estorn.";
            $arResumoReceita[$inCount]["total"]        = "Total Arrecadado";

            $inCount++;
        }
        while ( !$rsResumoReceita->eof() ) {

            if ( $tipo_receita == $rsResumoReceita->getCampo("tipo_receita") ) {

                if ( $rsResumoReceita->getCampo("tipo") == "O" ) {

                    $subTotalArrecadadoOrcamentario += $rsResumoReceita->getCampo("arrecadado");

                    $subTotalEstornadoOrcamentario  += $rsResumoReceita->getCampo("estornado");
                } elseif ( $rsResumoReceita->getCampo("tipo") == "E" ) {

                    $subTotalArrecadadoExtraOrcamentario += $rsResumoReceita->getCampo("arrecadado");

                    $subTotalEstornadoExtraOrcamentario  += $rsResumoReceita->getCampo("estornado");
                }

                $totalGeralArrecadado += $rsResumoReceita->getCampo("arrecadado");

                $totalGeralEstornado  += $rsResumoReceita->getCampo("estornado");

                $arResumoReceita[$inCount]["receita"]        = $rsResumoReceita->getCampo("receita");
                $arResumoReceita[$inCount]["tipo"]           = $rsResumoReceita->getCampo("tipo");
                $arResumoReceita[$inCount]["arrecadado"]     = number_format($rsResumoReceita->getCampo("arrecadado"),"2",",",".");
                $arResumoReceita[$inCount]["estornado"]      = "(".number_format($rsResumoReceita->getCampo("estornado"),"2",",",".").")";
                $total = $rsResumoReceita->getCampo("arrecadado") - $rsResumoReceita->getCampo("estornado");
                $arResumoReceita[$inCount]["total"]          = number_format($total,2,',','.');

                $stDescricao = $rsResumoReceita->getCampo("descricao");
                $stDescricao = str_replace( chr(10), "", $stDescricao );
                $stDescricao = wordwrap( $stDescricao, 50, chr(13) );
                $arDescricao = explode( chr(13), $stDescricao );
                foreach ($arDescricao as $stDescricao) {
                    $arResumoReceita[$inCount]["descricao"] = $stDescricao;
                    $inCount++;
                }

            } else {

                $arResumoReceita[$inCount]["receita"]        = "";
                $arResumoReceita[$inCount]["descricao"]      = "Total de Receitas Orçamentárias";
                $arResumoReceita[$inCount]["tipo"]           = "";
                $arResumoReceita[$inCount]["arrecadado"]     = number_format($subTotalArrecadadoOrcamentario,"2",",",".");
                $arResumoReceita[$inCount]["estornado"]      = "(".number_format($subTotalEstornadoOrcamentario ,"2",",",".").")";
                $arResumoReceita[$inCount]["total"]          = number_format($subTotalArrecadadoOrcamentario-$subTotalEstornadoOrcamentario ,"2",",",".");

                $inCount++;
                $arResumoReceita[$inCount]["receita"]      = "";
                $arResumoReceita[$inCount]["descricao"]    = "Total de Receitas Extra-Orçamentárias";
                $arResumoReceita[$inCount]["tipo"]         = "";
                $arResumoReceita[$inCount]["arrecadado"]   = number_format($subTotalArrecadadoExtraOrcamentario,"2",",",".");
                $arResumoReceita[$inCount]["estornado"]    = "(".number_format($subTotalEstornadoExtraOrcamentario ,"2",",",".").")";
                $arResumoReceita[$inCount]["total"]        = number_format($subTotalArrecadadoExtraOrcamentario-$subTotalEstornadoExtraOrcamentario ,"2",",",".");

                $subTotalArrecadado = $subTotalArrecadadoOrcamentario + $subTotalArrecadadoExtraOrcamentario;

                $subTotalEstornado  = $subTotalEstornadoOrcamentario  + $subTotalEstornadoExtraOrcamentario;

                $inCount++;
                $arResumoReceita[$inCount]["receita"]      = "";
                $arResumoReceita[$inCount]["descricao"]    = $label_subTotal;
                $arResumoReceita[$inCount]["tipo"]         = "";
                $arResumoReceita[$inCount]["arrecadado"]   = number_format($subTotalArrecadado,"2",",",".");
                $arResumoReceita[$inCount]["estornado"]    = "(".number_format($subTotalEstornado,"2",",",".").")";
                $arResumoReceita[$inCount]["total"]        = number_format($subTotalArrecadado - $subTotalEstornado,"2",",",".");

                $subTotalArrecadadoOrcamentario      = 0;

                $subTotalEstornadoOrcamentario       = 0;

                $subTotalArrecadadoExtraOrcamentario = 0;

                $subTotalEstornadoExtraOrcamentario  = 0;

                $inCount++;
                $arResumoReceita[$inCount]["receita"]      = "";
                $arResumoReceita[$inCount]["descricao"]    = "";
                $arResumoReceita[$inCount]["tipo"]         = "";
                $arResumoReceita[$inCount]["arrecadado"]   = "";
                $arResumoReceita[$inCount]["estornado"]    = "";
                $arResumoReceita[$inCount]["total"]        = "";

                $inCount++;
                $arResumoReceita[$inCount]["receita"]      = $label_receita;
                $arResumoReceita[$inCount]["descricao"]    = $rsResumoReceita->getCampo("tipo_receita");
                $arResumoReceita[$inCount]["tipo"]         = "";
                $arResumoReceita[$inCount]["arrecadado"]   = "";
                $arResumoReceita[$inCount]["estornado"]    = "";
                $arResumoReceita[$inCount]["total"]        = "";

                $inCount++;
                $arResumoReceita[$inCount]["receita"]      = $nome_campo;
                $arResumoReceita[$inCount]["descricao"]    = "Descrição";
                $arResumoReceita[$inCount]["tipo"]         = "Tipo";
                $arResumoReceita[$inCount]["arrecadado"]   = "Vlr. Arrec.";
                $arResumoReceita[$inCount]["estornado"]    = "Vlr. Estorn.";
                $arResumoReceita[$inCount]["total"]        = "Total";

                $inCount++;
                $arResumoReceita[$inCount]["receita"]      = $rsResumoReceita->getCampo("receita");
                $arResumoReceita[$inCount]["tipo"]         = $rsResumoReceita->getCampo("tipo");
                $arResumoReceita[$inCount]["arrecadado"]     = number_format($rsResumoReceita->getCampo("arrecadado"),"2",",",".");
                $arResumoReceita[$inCount]["estornado"]      = "(".number_format($rsResumoReceita->getCampo("estornado"),"2",",",".").")";
                $total = $rsResumoReceita->getCampo("arrecadado") - $rsResumoReceita->getCampo("estornado");
                $arResumoReceita[$inCount]["total"]          = number_format($total,2,',','.');

                $stDescricao = $rsResumoReceita->getCampo("descricao");
                $stDescricao = str_replace( chr(10), "", $stDescricao );
                $stDescricao = wordwrap( $stDescricao, 50, chr(13) );
                $arDescricao = explode( chr(13), $stDescricao );
                foreach ($arDescricao as $stDescricao) {
                    $arResumoReceita[$inCount]["descricao"] = $stDescricao;
                    $inCount++;
                }

                if ( $rsResumoReceita->getCampo("tipo") == "O" ) {

                    $subTotalArrecadadoOrcamentario += $rsResumoReceita->getCampo("arrecadado");

                    $subTotalEstornadoOrcamentario  += $rsResumoReceita->getCampo("estornado");
                } elseif ( $rsResumoReceita->getCampo("tipo") == "E" ) {

                    $subTotalArrecadadoExtraOrcamentario += $rsResumoReceita->getCampo("arrecadado");

                    $subTotalEstornadoExtraOrcamentario  += $rsResumoReceita->getCampo("estornado");
                }

                $totalGeralArrecadado += $rsResumoReceita->getCampo("arrecadado");

                $totalGeralEstornado  += $rsResumoReceita->getCampo("estornado");

            }

            $tipo_receita = $rsResumoReceita->getCampo("tipo_receita");

            $rsResumoReceita->proximo();
        }

        $arResumoReceita[$inCount]["receita"]        = "";
        $arResumoReceita[$inCount]["descricao"]      = "Total de Receitas Orçamentárias";
        $arResumoReceita[$inCount]["tipo"]           = "";
        $arResumoReceita[$inCount]["arrecadado"]     = number_format($subTotalArrecadadoOrcamentario,"2",",",".");
        $arResumoReceita[$inCount]["estornado"]      = "(".number_format($subTotalEstornadoOrcamentario,"2",",",".").")";
        $arResumoReceita[$inCount]["total"]          = number_format($subTotalArrecadadoOrcamentario-$subTotalEstornadoOrcamentario ,"2",",",".");

        $inCount++;
        $arResumoReceita[$inCount]["receita"]      = "";
        $arResumoReceita[$inCount]["descricao"]    = "Total de Receitas Extra-Orçamentárias";
        $arResumoReceita[$inCount]["tipo"]         = "";
        $arResumoReceita[$inCount]["arrecadado"]   = number_format($subTotalArrecadadoExtraOrcamentario,"2",",",".");
        $arResumoReceita[$inCount]["estornado"]    = "(".number_format($subTotalEstornadoExtraOrcamentario,"2",",",".").")";
        $arResumoReceita[$inCount]["total"]        = number_format($subTotalArrecadadoExtraOrcamentario-$subTotalEstornadoExtraOrcamentario ,"2",",",".");

        $subTotalArrecadado = $subTotalArrecadadoOrcamentario + $subTotalArrecadadoExtraOrcamentario;

        $subTotalEstornado  = $subTotalEstornadoOrcamentario  + $subTotalEstornadoExtraOrcamentario;

        $inCount++;
        $arResumoReceita[$inCount]["receita"]        = "";
        $arResumoReceita[$inCount]["descricao"]      = $label_subTotal;
        $arResumoReceita[$inCount]["tipo"]           = "";
        $arResumoReceita[$inCount]["arrecadado"]     = number_format(($subTotalArrecadado),"2",",",".");
        $arResumoReceita[$inCount]["estornado"]      = "(".number_format(($subTotalEstornado),"2",",",".").")";
        $arResumoReceita[$inCount]["total"]          = number_format($subTotalArrecadado - $subTotalEstornado,"2",",",".");

        if ( $this->getTipoRelatorio() != "" ) {

            $inCount++;
            $arResumoReceita[$inCount]["receita"]      = "";
            $arResumoReceita[$inCount]["descricao"]    = "";
            $arResumoReceita[$inCount]["tipo"]         = "";
            $arResumoReceita[$inCount]["arrecadado"]   = "";
            $arResumoReceita[$inCount]["estornado"]    = "";
            $arResumoReceita[$inCount]["total"]        = "";

            $inCount++;
            $arResumoReceita[$inCount]["receita"]      = "";
            $arResumoReceita[$inCount]["descricao"]    = "Total Geral das Receitas";
            $arResumoReceita[$inCount]["tipo"]         = "";
            $arResumoReceita[$inCount]["arrecadado"]   = number_format($totalGeralArrecadado,"2",",",".");
            $arResumoReceita[$inCount]["estornado"]    = "(".number_format($totalGeralEstornado,"2",",",".").")";
            $arResumoReceita[$inCount]["total"]        = number_format($totalGeralArrecadado - $totalGeralEstornado,"2",",",".");
        }
    }

    $rsResumoReceita  = new RecordSet;

    $rsResumoReceita->preenche($arResumoReceita);
//    $rsResumoReceita->debug(); die;

    $rsRecordSet = array( $rsResumoReceita );

    return $obErro;

}

}
