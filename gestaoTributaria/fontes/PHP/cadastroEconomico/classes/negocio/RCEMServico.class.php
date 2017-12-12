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
    * Classe de regra de negócio para Serviço
    * Data de Criação: 22/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMServico.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.03
*/

/*
$Log$
Revision 1.14  2006/11/23 16:05:20  cercato
bug #7573#

Revision 1.13  2006/11/03 11:08:34  cercato
bug #7314#

Revision 1.12  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNivelServico.class.php"        );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMServico.class.php"        );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMNivelServicoValor.class.php"   );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAliquotaServico.class.php");
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMServicoAtividade.class.php");

class RCEMServico extends RCEMNivelServico
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoAtividade;

/**
    * @access Private
    * @var Integer
*/
var $inCodigoServico;
/**
    * @access Private
    * @var String
*/
var $stNomeServico;
/**
    * @access Private
    * @var Float
*/
var $flAliquotaServico;
/**
    * @access Private
    * @var String
*/
var $dtDataInicio;
/**
    * @access Private
    * @var Date
*/
var $stValor;//tabela SERVICO_NIVEL
/**
    * @access Private
    * @var String
*/
var $stValorComposto;//valor de todos os niveis de servicos concateneados
/**
    * @access Private
    * @var String
*/
var $stValorReduzido;//valor de todos os niveis que possuem servicos
/**
    * @access Private
    * @var Object
*/
var $obTCEMServicoNivel;
/**
    * @access Private
    * @var Object
*/
var $obTCEMServico;

/**
    * @access Private
    * @var Array
*/
var $arChaveServico;
/**
    * @access Boolean
    * @var Array
*/
var $boAtivo;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoAtividade($valor) { $this->inCodigoAtividade = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoServico($valor) { $this->inCodigoServico = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomeServico($valor) { $this->stNomeServico = $valor; }
/**
    * @access Public
    * @param Float $valor
*/
function setAliquotaServico($valor) { $this->flAliquotaServico = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataInicio($valor) { $this->dtDataInicio = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValor($valor) { $this->stValor = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValorComposto($valor) { $this->stValorComposto = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValorReduzido($valor) { $this->stValorReduzido = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setAtivo($valor) { $this->boAtivo = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoAtividade() { return $this->inCodigoAtividade; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoServico() { return $this->inCodigoServico; }
/**
    * @access Public
    * @return String
*/
function getNomeServico() { return $this->stNomeServico;   }
/**
    * @access Public
    * @return Float
*/
function getAliquotaServico() { return $this->flAliquotaServico;   }
/**
    * @access Public
    * @param Date $valor
*/
function getDataInicio() { return $this->dtDataInicio; }
/**
    * @access Public
    * @return String
*/
function getValor() { return $this->stValor;            }
/**
    * @access Public
    * @return String
*/
function getValorComposto() { return $this->stValorComposto;    }
/**
    * @access Public
    * @return String
*/
function getValorReduzido() { return $this->stValorReduzido;    }
/**
    * @access Public
    * @return Boolean
*/
function isAtivo() { return $this->boAtivo;    }

/**
     * Método construtor
     * @access Private
*/
function RCEMServico()
{
    parent::RCEMNivelServico();
    $this->obTCEMNivelServicoValor  = new TCEMNivelServicoValor;
    $this->obTCEMServico            = new TCEMServico;
    $this->obTCEMAliquotaServico    = new TCEMAliquotaServico;
    $this->arChaveServico           = array();
}

/**
    * Inclui os dados referentes a Servico
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirServico($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->validaCodigoServico();

        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTCEMServico->proximoCod( $this->inCodigoServico, $boTransacao );

            if ( !$obErro->ocorreu() ) {
                //MONTA CODIGO ESTRUTURAL
                $obErro = $this->recuperaMascaraNiveis( $rsMascaraNivel, $boTransancao );
                $rsMascaraNivel->ordena('cod_nivel');
                $obErro = $this->consultarNivel( $boTransacao );

                $stCodigoMascara = $this->arChaveServico[ count($this->arChaveServico) - 1 ][3].".";
                $stCodigoMascara .= str_pad( $this->stValor, strlen($this->stMascara), "0", STR_PAD_LEFT );
                $stMascaraComposta = "";
                $rsMascaraNivel->setPrimeiroElemento();

                $i = 1;

                while ( !$rsMascaraNivel->eof() ) {
                    $stMascaraComposta .= $rsMascaraNivel->getCampo("mascara").".";
                    $stMascaraNivel = str_replace( "9", "0", $rsMascaraNivel->getCampo("mascara") );
                    $stMascaraNivel = preg_replace("[A-Za-z]","0",$stMascaraNivel);
                    $stCodigoComposto .= $rsMascaraNivel->getCampo("cod_nivel") == $this->getCodigoNivel() ? $stCodigoMascara."." : $stMascaraNivel.".";
                    $rsMascaraNivel->proximo();
                }

                $stMascaraComposta = substr( $stMascaraComposta, 0, strlen( $stMascara ) - 1 );
                $stCodigoComposto  = substr( $stCodigoComposto, 0, strlen( $stMascara ) - 1);

                $corteMascara     = strlen($stCodigoComposto) - strlen($stMascaraComposta);
                $stCodigoComposto = substr( $stCodigoComposto, $corteMascara );

                //EXECUTA A INCLUSAO NA TABELA SERVICO
                $this->obTCEMServico->setDado( "cod_servico", $this->inCodigoServico );
                $this->obTCEMServico->setDado( "nom_servico", $this->stNomeServico   );
                $this->obTCEMServico->setDado( "cod_estrutural", $stCodigoComposto   );
                $obErro = $this->obTCEMServico->inclusao( $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    //LISTA OS NIVEIS EM RELAÇÃO A VIGÊNCIA SELECONADA
                    $inCodigoNivelTmp = $this->inCodigoNivel;
                    $this->inCodigoNivel = "";
                    $obErro = $this->listarNiveis( $rsNiveis, $boTransacao );
                    $this->inCodigoNivel = $inCodigoNivelTmp;
                    if ( !$obErro->ocorreu() ) {
                        $this->obTCEMNivelServicoValor->setDado( "cod_vigencia",  $this->inCodigoVigencia  );
                        //EXECUTA A INCLUSAO DOS VALORES DOS SERVICOS NOS NIVEIS SUPERIORES AO CORRENTE
                        foreach ($this->arChaveServico as $arChaveServico) {
                            //[0] = cod_nivel | [1] = cod_servico | [2] = valor
                            $this->obTCEMNivelServicoValor->setDado( "cod_nivel"      , $arChaveServico[0] );
                             $this->obTCEMNivelServicoValor->setDado( "cod_servico", $this->inCodigoServico );
                            //MASCARA O VALOR CONFORME O MASCARA DO NIVEL
                            $stValor = $arChaveServico[2];
                            $this->obTCEMNivelServicoValor->setDado( "valor"          , $arChaveServico[2] );
                            $obErro = $this->obTCEMNivelServicoValor->inclusao( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                            if ( !$rsNiveis->eof() ) {
                                $rsNiveis->proximo();
                            }
                        }

                        //INCLUI O VALOR DA SERVICO NO NIVEL CORRENTE
                        $this->obTCEMNivelServicoValor->setDado( "cod_servico", $this->inCodigoServico );
                        $this->obTCEMNivelServicoValor->setDado( "cod_nivel"  , $this->inCodigoNivel   );
                        $stValor = $this->stValor;
                        $this->obTCEMNivelServicoValor->setDado( "valor", $this->getValor() );
                        $obErro = $this->obTCEMNivelServicoValor->inclusao( $boTransacao );

                        if ( !$rsNiveis->eof() ) {
                            $rsNiveis->proximo();
                        }
                        //INCLUI O VALOR DA SERVICO DOS NIVEIS SEGUINTES
                        if ( !$obErro->ocorreu() ) {
                            while ( !$rsNiveis->eof() ) {
                                $stValor = "0";
                                $this->obTCEMNivelServicoValor->setDado( "cod_nivel"      , $rsNiveis->getCampo("cod_nivel") );
                                $this->obTCEMNivelServicoValor->setDado( "valor", $stValor );
                                $obErro = $this->obTCEMNivelServicoValor->inclusao( $boTransacao );
                                if ( $obErro->ocorreu() ) {
                                    break;
                                }
                                if ( !$rsNiveis->eof() ) {
                                    $rsNiveis->proximo();
                                }
                            }

                            // DADOS DE ALIQUOTA
                            if ( !$obErro->ocorreu() ) {

                                if ($this->flAliquotaServico != "") {

                                    $this->obTCEMAliquotaServico->setDado("cod_servico", $this->inCodigoServico );
                                    $this->obTCEMAliquotaServico->setDado( "valor"     ,$this->flAliquotaServico);
                                    $this->obTCEMAliquotaServico->setDado( "dt_vigencia", date('Y-m-d') );
                                    $obErro = $this->obTCEMAliquotaServico->inclusao( $boTransacao );
                                }
                                if ( !$obErro->ocorreu() ) {
                                    $this->obTCEMServico->setDado( "valor" , $stCodigoComposto);
                                    $obErro = $this->obTCEMServico->atualizaServico( $boTransacao );
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMServico );

    return $obErro;
}

/**
    * Altera os dados do Servico setada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarServico($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obErro = $this->validaCodigoServico();
        if ( !$obErro->ocorreu() ) {
        //$obErro = $this->validaCodigoServico( $boTransacao );

            if ( !$obErro->ocorreu() ) {
            //EXECUTA A INCLUSAO NA TABELA SERVICO

                $this->obTCEMNivelServicoValor->setDado( "cod_vigencia"   , $this->inCodigoVigencia    );
                $this->obTCEMNivelServicoValor->setDado( "cod_nivel"      , $this->inCodigoNivel       );
                $this->obTCEMNivelServicoValor->setDado( "cod_servico"    , $this->inCodigoServico );
                $stValor = $this->stValor;
                $this->obTCEMNivelServicoValor->setDado( "valor"          , $this->getValor () );
                $obErro = $this->obTCEMNivelServicoValor->alteracao( $boTransacao );

                    // DADOS DE ALIQUOTA

                if ( !$obErro->ocorreu() ) {
                    $this->obTCEMServico->setDado( "cod_servico", $this->inCodigoServico );
                    $this->obTCEMServico->setDado( "nom_servico", $this->stNomeServico   );
                    $this->obTCEMServico->setDado( "cod_estrutural", $this->getValorComposto()   );

                    $obErro = $this->obTCEMServico->alteracao( $boTransacao );

                    if ( !$obErro->ocorreu() ) {
                        if ($this->flAliquotaServico != "") {
                            $obErro = $this->alterarAliquota( $boTransacao );
                        }
                        if ( !$obErro->ocorreu() ) {
                            //ATUALIZAR A TABELA NIVEL_SERVICO_VALOR ANTES
                            $this->obTCEMNivelServicoValor->setDado("nivel" , $_REQUEST['inCodigoNivel']);
                            $this->obTCEMNivelServicoValor->setDado("valor" , $this->getValor() );
                            $this->obTCEMNivelServicoValor->setDado("valorAntigo" , $_REQUEST['stValorServicoAntigo'] );
                            $obErro = $this->obTCEMNivelServicoValor->atualizaNivelServicoValor( $boTransacao);

                            $this->obTCEMServico->setDado( "valor" , $this->getValorReduzido() );
                            $obErro = $this->obTCEMServico->atualizaServico( $boTransacao );
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMServico );

    return $obErro;
}

/**
    * Exclui a Servico setada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirServico($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $inCodigoServicoTmp = $this->inCodigoServico;
        $inCodigoNivelTmp       = $this->inCodigoNivel;
        $this->inCodigoServico = "";
        $this->inCodigoNivel   = "";
        $obErro = $this->verificaFilhosServico( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->inCodigoServico = $inCodigoServicoTmp;
            $obErro = $this->listarNiveis( $rsNivel, $boTransacao );

            if ( !$obErro->ocorreu() ) {

                while ( !$rsNivel->eof() ) {
                    $this->obTCEMNivelServicoValor->setDado( "cod_servico", $this->inCodigoServico      );
                    $this->obTCEMNivelServicoValor->setDado( "cod_vigencia",    $this->inCodigoVigencia         );
                    $this->obTCEMNivelServicoValor->setDado( "cod_nivel",       $rsNivel->getCampo("cod_nivel") );
                    $obErro = $this->obTCEMNivelServicoValor->exclusao( $boTransacao );

                    if ( $obErro->ocorreu() ) {
                        break;
                    }

                    $rsNivel->proximo();
                }
            }

            $this->inCodigoNivel = $inCodigoNivelTmp;

            if ( !$obErro->ocorreu() ) {
                if ($this->flAliquotaServico != "") {
                    $this->obTCEMAliquotaServico->setDado( "cod_servico", $this->inCodigoServico  );
                    $this->obTCEMAliquotaServico->setDado( "valor"      , $this->flAliquotaServico);
                    $obErro = $this->obTCEMAliquotaServico->exclusao( $boTransacao );
                }

                if ( !$obErro->ocorreu() ) {
                    $TCEMServicoAtividade = new TCEMServicoAtividade;
                    $stFiltroServicoAtividade = ' WHERE cod_servico = '.$this->inCodigoServico.'';
                    $TCEMServicoAtividade->recuperaTodos($rsServicoAtividade, $stFiltroServicoAtividade, '',$boTransacao);
                    if ($rsServicoAtividade->getNumLinhas() > 0 ) {
                        $obErro->setDescricao('Erro ao Excluir Serviço! Os dados estão sendo utilizados!');
                    } else {
                        $this->obTCEMServico->setDado( "cod_servico", $this->inCodigoServico );
                        $obErro = $this->obTCEMServico->exclusao( $boTransacao );
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMServico );

    return $obErro;
}

/**
    * Lista os Servicos segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarServico(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " AND LN.COD_VIGENCIA = ".$this->inCodigoVigencia." ";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " AND LN.COD_NIVEL = ".$this->inCodigoNivel." ";
    }
    if ($this->inCodigoServico) {
        $stFiltro .= " AND LN.COD_SERVICO = ".$this->inCodigoServico." ";
    }
    if ($this->stNomeServico) {
        $stFiltro .= " AND UPPER(LO.NOM_SERVICO) LIKE UPPER('%".$this->stNomeServico."%') ";
    }
    if ($this->stNomeNivel) {
        $stFiltro .= " AND UPPER(NI.NOM_NIVEL) LIKE UPPER('%".$this->stNomeNivel."%') ";
    }
    if ($this->stValorReduzido and  $this->stNomeNivel == 1) {
        $stFiltro .= " AND valor_reduzido like '".$this->stValorReduzido."%' ";
    } elseif ($this->stValorReduzido) {
        $stFiltro .= " AND valor_reduzido like '".$this->stValorReduzido.".%' ";
    }
    if ($this->stValorComposto) {
        $stFiltro .= " AND valor_composto like '".trim($this->stValorComposto)."%' ";
    }

    $stOrdem = " ORDER BY LN.valor_composto";

    $obErro = $this->obTCEMServico->recuperaServicoAtivo( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao, $this->inCodigoAtividade );

    return $obErro;
}

/**
    * Lista os Servicos para aliquota segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarServicoAliquota(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " AND LN.COD_VIGENCIA = ".$this->inCodigoVigencia." ";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " AND LN.COD_NIVEL = ".$this->inCodigoNivel." ";
    }
    if ($this->inCodigoServico) {
        $stFiltro .= " AND LN.COD_SERVICO = ".$this->inCodigoServico." ";
    }
    if ($this->stNomeServico) {
        $stFiltro .= " AND UPPER(LO.NOM_SERVICO) LIKE UPPER('%".$this->stNomeServico."%') ";
    }
    if ($this->stNomeNivel) {
        $stFiltro .= " AND UPPER(NI.NOM_NIVEL) LIKE UPPER('%".$this->stNomeNivel."%') ";
    }
    if ($this->stValorReduzido and  $this->stNomeNivel == 1) {
        $stFiltro .= " AND valor_reduzido like '".$this->stValorReduzido."%' ";
    } elseif ($this->stValorReduzido) {
        $stFiltro .= " AND valor_reduzido like '".$this->stValorReduzido.".%' ";
    }
    if ($this->stValorComposto) {
        $stFiltro .= " AND valor_composto like '".$this->stValorComposto."%' ";
    }
    $stOrdem = " ORDER BY LN.valor_composto";
    $obErro = $this->obTCEMServico->recuperaServicoAliquota( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Recupera do banco de dados os dados do Servico selecionada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarServico($boTransacao = "")
{
    $obErro = new Erro;
    if ($this->inCodigoVigencia and $this->inCodigoNivel and $this->inCodigoServico) {
        $obErro = $this->listarServico( $rsServico, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->stNomeServico     = $rsServico->getCampo( "nom_servico" );
            $this->stNomeNivel       = $rsServico->getCampo( "nom_nivel" );
            $this->stNomeServico     = $rsServico->getCampo( "nom_servico" );
            $this->stMascara         = $rsServico->getCampo( "mascara" );
            $this->stValorComposto   = $rsServico->getCampo( "valor_composto" );
            $this->stValorReduzido   = $rsServico->getCampo( "valor_reduzido" );
            $arValor = explode( ".", $this->stValorReduzido );
            $this->stValor           = end( $arValor );
        }
    }

    return $obErro;
}

/**
    * Verifica se existem filhos do servico setadas, se houver retorna o erro informando
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaFilhosServico($boTransacao = "")
{
    $inCodigoServicoTmp = $this->inCodigoServico;
    $inCodigoNivelTmp       = $this->inCodigoNivel;
    $this->inCodigoServico = "";
    $this->inCodigoNivel       = "";
    $obErro = $this->listarServico( $rsListaServico, $boTransacao );
    if ( !$obErro->ocorreu() and $rsListaServico->getNumLinhas() > 0 ) {
        $obErro->setDescricao( "Existem serviços dependentes deste serviço!" );
    }
    $this->inCodigoServico = $inCodigoServicoTmp;
    $this->inCodigoNivel   = $inCodigoNivelTmp;

    return $obErro;
}

/**
    * Valida o codigo do servico
    * @access Public
    * @param Integer $inCodigo Codigo do nivel superior
*/
function validaCodigoServico($boTransacao = "")
{
    $stFiltro = "";

    if ($this->inCodigoVigencia) {
        $stFiltro .= " AND LN.COD_VIGENCIA = ".$this->inCodigoVigencia." ";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " AND LN.COD_NIVEL = ".$this->inCodigoNivel." ";
    }
    //if ($this->inCodigoServico) {
      //  $stFiltro .= " AND LN.COD_SERVICO <> ".$this->inCodigoServico." ";
   // }
    if ($this->stValor) {
        $stFiltro .= " AND LPAD( LN.valor, length(NI.mascara),'0' ) =";
        $stFiltro .= " LPAD( '".$this->stValor."', length(NI.mascara), '0' ) ";
    }
    if ($_REQUEST['inCodigoServico']) {
        $stFiltro .= " AND ltrim (LN.valor_reduzido, '0') like '". $_REQUEST['inCodigoServico'] . "' ";
    }

    if ($_REQUEST['stAcao'] == 'alterar') {
        $stFiltro .= " AND LN.COD_SERVICO <> ". $this->inCodigoServico . " ";
    }

    $obErro = $this->obTCEMServico->recuperaServicoAtivo( $rsRecordSet, $stFiltro, "" , $boTransacao );
    $contx = 0;
    while ( $contx < $rsRecordSet->getNumLinhas() ) {

        if ($_REQUEST['stAcao'] == 'alterar') {
            $valorCompostoTMPx = explode ('.', $this->getValorReduzido(), $this->getCodigoNivel() );
            $cont = 0; $valorCompostoTMP='';

            while ( $cont < count ( $valorCompostoTMPx )-1 ) {
                $valorCompostoTMP .= $valorCompostoTMPx[$cont].'.';
                $cont++;
            }
            $valorCompostoTMP = substr ( $valorCompostoTMP, 0 , strlen($valorCompostoTMP)-1 );

        } else {
            $valorCompostoTMP = $this->getValorComposto();
        }

        $valorReduzidoTabela = $rsRecordSet->getCampo ('valor_reduzido');
        $cont = 0;
        $valorUltimoNivel = '';
        $chaveTMP = explode ( '.', $valorReduzidoTabela, $this->getCodigoNivel() );
        while ( $cont < ($this->getCodigoNivel()-1) ) {
            $valorUltimoNivel .= $chaveTMP[$cont].'.';
            $cont++;
        }
        $valorUltimoNivel = substr( $valorUltimoNivel, 0, strlen ($valorUltimoNivel) -1 );

        //echo '<br>'.$valorCompostoTMP . ' - ' . $valorUltimoNivel;
        if ($valorCompostoTMP == $valorUltimoNivel) {
            $obErro->setDescricao( "Já existe um serviço cadastrada com o código ".$this->stValor . " para este nível!" );
        }
        $contx++;
        $rsRecordSet->proximo();
    }
    //exit;
    return $obErro;
}

/**
    * Adiciona no array  arChaveServico os códigos dos serviços ao de niveis superiores
    * @access Public
    * @param Integer $inCodigo Codigo do nivel superior
*/
function addCodigoServico($arChaveServico)
{
    $this->arChaveServico[] = $arChaveServico;//[0] = cod_nivel | [1] = cod_servico | [2] = valor
}

/**
    * Lista os Servicos segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarAliquotaServico(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoServico) {
        $stFiltro = " COD_SERVICO = ".$this->inCodigoServico. " AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY COD_SERVICO ";
    $obErro = $this->obTCEMAliquotaServico->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Altera os dados da Aliquota setada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarAliquota($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //EXECUTA A INCLUSAO NA TABELA ALIQUOTA SERVICO
        $this->obTCEMAliquotaServico->setDado( "cod_servico" , $this->inCodigoServico   );
        $this->obTCEMAliquotaServico->setDado( "valor"       , $this->flAliquotaServico );
        $this->obTCEMAliquotaServico->setDado( "dt_vigencia" , $this->dtDataInicio      );
        $obErro = $this->obTCEMAliquotaServico->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMAliquotaServico );

    return $obErro;
}

}

?>
