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
    * Regra de negócio para Definição de Calendário Fiscal
    * Data de Criação   : 18/05/2005

    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @subpackage Regras
    * @package Urbem

    * $Id: RARRCalendarioFiscal.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.03

*/

/*
$Log$
Revision 1.10  2006/10/23 10:23:58  cercato
setando ano_exercicio nas consultas.

Revision 1.9  2006/09/15 11:50:14  fabio
corrigidas tags de caso de uso

Revision 1.8  2006/09/15 10:48:45  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GT_ARR_MAPEAMENTO."TARRCalendarioFiscal.class.php"        );
include_once (CAM_GT_ARR_NEGOCIO."RARRGrupoVencimento.class.php"              );
include_once (CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"               );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRGrupoCredito.class.php"           );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRGrupoVencimento.class.php"        );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRAtributoCalendarioValor.class.php");

class RARRCalendarioFiscal
{
    /**
        * @access Private
        * @param Integer
    */
    public $inValorMinimo;
    /**
        * @access Private
        * @param Integer
    */
    public $inValorMinimoParcela;
    /**
        * @access Private
        * @param Object
    */
    public $obTARRCalendarioFiscal;
    /**
        * @access Private
        * @param Object
    */
    public $obRCadastroDinamico;
    /**
        * @access Private
        * @param Object
    */
    public $obTARRAtributoCalendarioValor;
    /**
        * @access Private
        * @param Object
    */
    public $inValorMinimoIntegral;
    /**
        * @access Private
        * @param Object
    */
    public $roUltimoGrupoVencimento;
    /**
        * @access Private
        * @param Object
    */
    public $inCodigoGrupo;

    /**
        * @access Private
        * @param integer
    */
    public $inAnoExercicio;

    // SETTERS
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setAnoExercicio($valor)
    {
        $this->inAnoExercicio = $valor;
    }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setValorMinimo($valor)
    {
        $this->inValorMinimo = $valor;
    }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setValorMinimoParcela($valor)
    {
        $this->inValorMinimoParcela = $valor;
    }
    /**
        * @access Public
        * @param Object $valor
    */
    public function setTARRCalendarioFiscal($valor)
    {
        $this->obTARRCalendarioFiscal = $valor;
    }
    /**
        * @access Public
        * @param Object $valor
    */
    public function setRCadastroDinamico($valor)
    {
        $this->obRCadastroDinamico = $valor;
    }
    /**
        * @access Public
        * @param Object $valor
    */
    public function setTARRAtributoCalndarioValor($valor)
    {
        $this->obTARRAtributoCalendarioValor = $valor;
    }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setValorMinimoIntegral($valor)
    {
        $this->inValorMinimoIntegral = $valor;
    }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setCodigoGrupo($valor)
    {
        $this->inCodigoGrupo = $valor;
    }

    // GETTERS
    /**
        * @access Public
        * @return Integer
    */
    public function getAnoExercicio()
    {
        return $this->inAnoExercicio;
    }
    /**
         * @access Public
         * @return Integer
    */
    public function getValorMinimo()
    {
        return $this->inValorMinimo;
    }
    /**
         * @access Public
         * @return Integer
    */
    public function getValorMinimoParcela()
    {
        return $this->inValorMinimoParcela;
    }
    /**
        * @access Public
        * @return Object
    */
    public function getTARRCalendarioFiscal()
    {
        return $this->obTARRCalendarioFiscal;
    }
    /**
        * @access Public
        * @return Object
    */
    public function getRCadastroDinamico()
    {
        return $this->obRCadastroDinamico;
    }
    /**
        * @access Public
        * @return Object
    */
    public function getTARRAtributoCalendarioValor()
    {
        return $this->obTARRAtributoCalendarioValor;
    }
    /**
         * @access Public
         * @return Integer
    */
    public function getValorMinimoIntegral()
    {
        return $this->inValorMinimoIntegral;
    }
    /**
         * @access Public
         * @return Integer
    */
    public function getCodigoGrupo()
    {
        return $this->inCodigoGrupo;
    }

    /**
        * Metodo construtor
        * @access Private
    */
    public function RARRCalendarioFiscal()
    {
        $this->obTransacao            = new Transacao;
        $this->obTARRCalendarioFiscal = new TARRCalendarioFiscal;
        $this->obRCadastroDinamico    = new RCadastroDinamico;
        $this->obTARRGrupoCredito     = new TARRGrupoCredito;
        $this->obTARRGrupoVencimento  = new TARRGrupoVencimento;
        $this->obRCadastroDinamico->setPersistenteValores  ( new TARRAtributoCalendarioValor );
        $this->obRCadastroDinamico->setCodCadastro         ( 1 );
        $this->obRCadastroDinamico->obRModulo->setCodModulo( 25 );
        $this->arRARRGrupoVencimento  = array();
    }

    /**
        * Adiciona um objeto Grupo Vencimento no array de Grupos de Vencimento
        * @access Public
    */
    public function addCalendarioGrupoVencimento()
    {
        $this->arRARRGrupoVencimento[] = new RARRGrupoVencimento( $this );
        $this->roUltimoGrupoVencimento = &$this->arRARRGrupoVencimento[ count($this->arRARRGrupoVencimento) - 1 ];
    }

    public function listarGrupoCredito(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->getCodigoGrupo() ) {
            $stFiltro .= " cod_grupo = ".$this->getCodigoGrupo()." and ";
        }

        if ( $this->getAnoExercicio() ) {
            $stFiltro .= " ano_exercicio = '".$this->getAnoExercicio()."' and ";
        }

        if ($stFiltro) {
            $stFiltro = " where ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
        }

        $stOrdem  = " ORDER BY COD_GRUPO ";

        $obErro = $this->obTARRGrupoCredito->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function listarGrupoVencimentos(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";

        if ( $this->getCodigoGrupo() ) {
            $stFiltro .= " AND GC.COD_GRUPO = ". $this->getCodigoGrupo();
        }

        if ( $this->getAnoExercicio() ) {
            $stFiltro .= " AND GC.ANO_EXERCICIO = '".$this->getAnoExercicio()."'";
        }

        $stOrdem = " ORDER BY COD_GRUPO ";

        $obErro = $this->obTARRCalendarioFiscal->recuperaGrupoVencimentos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    /**
        * Definir celendario
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function definirCalendario($boTransacao = "")
    {
        $boFlagTransacao = false;

        $this->listarCalendario( $rsListaCalendario );
        if ( !$rsListaCalendario->Eof() ) {
            $obErro = new Erro;
            $obErro->setDescricao( "Calendario Fiscal para o grupo ".$this->getCodigoGrupo()."/".$this->getAnoExercicio()." já definido!" );

            return $obErro;
        }

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTARRCalendarioFiscal->setDado( "cod_grupo"               , $this->getCodigoGrupo() );
            $this->obTARRCalendarioFiscal->setDado( "valor_minimo"            , $this->getValorMinimo() );
            $this->obTARRCalendarioFiscal->setDado( "valor_minimo_lancamento" , $this->getValorMinimoIntegral() );
            $this->obTARRCalendarioFiscal->setDado( "valor_minimo_parcela"    , $this->getValorMinimoParcela() );
            $this->obTARRCalendarioFiscal->setDado( "ano_exercicio"           , $this->getAnoExercicio() );

            $obErro = $this->obTARRCalendarioFiscal->inclusao( $boTransacao );
            //$this->obTARRCalendarioFiscal->debug();exit;
            if ( !$obErro->ocorreu() ) {
                foreach ($this->arRARRGrupoVencimento as $obRARRGrupoVencimento) {
                    $this->obTARRGrupoVencimento->setDado( "cod_grupo"      , $this->getCodigoGrupo() );
                    $this->obTARRGrupoVencimento->setDado( "ano_exercicio"  , $this->getAnoExercicio() );
                    $this->obTARRGrupoVencimento->setDado( "cod_vencimento" , $obRARRGrupoVencimento->getCodigoVencimento() );

                    $this->obTARRGrupoVencimento->setDado( "descricao"      , $obRARRGrupoVencimento->getDescricao() );

                    $this->obTARRGrupoVencimento->setDado( "data_vencimento_parcela_unica" , $obRARRGrupoVencimento->getVencimentoValorIntegral() );

                    $this->obTARRGrupoVencimento->setDado( "limite_inicial" , $obRARRGrupoVencimento->getLimiteInicial() );

                    $this->obTARRGrupoVencimento->setDado( "limite_final" , $obRARRGrupoVencimento->getLimiteFinal() );

                    $this->obTARRGrupoVencimento->setDado( "utilizar_unica" , $obRARRGrupoVencimento->getUtilizarCotaUnica() );

                    $obErro = $this->obTARRGrupoVencimento->inclusao( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
                if ( !$obErro->ocorreu() ) {
                    $arChaveAtributoCalendario =  array( "cod_grupo" => $this->getCodigoGrupo() );
                    $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCalendario );
                    $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRCalendarioFiscal);

        return $obErro;
    }

    /**
        * Alterar celendario
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function alterarCalendario($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTARRCalendarioFiscal->setDado( "cod_grupo"               , $this->getCodigoGrupo() );
            $this->obTARRCalendarioFiscal->setDado( "valor_minimo"            , $this->getValorMinimo() );
            $this->obTARRCalendarioFiscal->setDado( "valor_minimo_lancamento" , $this->getValorMinimoIntegral() );
            $this->obTARRCalendarioFiscal->setDado( "valor_minimo_parcela"    , $this->getValorMinimoParcela() );
            $this->obTARRCalendarioFiscal->setDado( "ano_exercicio"           , $this->getAnoExercicio() );
            $obErro = $this->obTARRCalendarioFiscal->alteracao( $boTransacao );

            if ( !$obErro->ocorreu() ) {
                $inTmp = $this->roUltimoGrupoVencimento->getCodigoVencimento();
                $this->roUltimoGrupoVencimento->setCodigoVencimento("");
                $obErro = $this->roUltimoGrupoVencimento->listarGrupoVencimento( $rsRecordSet , $boTransacao );
                $this->roUltimoGrupoVencimento->setCodigoVencimento($inTmp);
                if ( !$obErro->ocorreu() ) {
                    $arGrupo = array();
                    while ( !$rsRecordSet->eof() ) {
                        $inChaveGrupo = $rsRecordSet->getCampo( "cod_grupo" ).".".$rsRecordSet->getCampo( "cod_vencimento" );
                        $arGrupo[$inChaveGrupo] = true;
                        $rsRecordSet->proximo();
                    }
                    foreach ($this->arRARRGrupoVencimento as $obRARRGrupoVencimento) {
                        $inChave = $this->getCodigoGrupo().".".$obRARRGrupoVencimento->getCodigoVencimento();
                        if ( !isset( $arGrupo[$inChave] ) ) {
                            $this->obTARRGrupoVencimento->setDado( "ano_exercicio"  , $this->getAnoExercicio() );
                            $this->obTARRGrupoVencimento->setDado( "cod_grupo"      , $this->getCodigoGrupo() );
                            $this->obTARRGrupoVencimento->setDado( "cod_vencimento" , $obRARRGrupoVencimento->getCodigoVencimento() );

                            $this->obTARRGrupoVencimento->setDado( "descricao"      , $obRARRGrupoVencimento->getDescricao() );
                            $this->obTARRGrupoVencimento->setDado( "data_vencimento_parcela_unica" , $obRARRGrupoVencimento->getVencimentoValorIntegral());
                            $this->obTARRGrupoVencimento->setDado( "limite_inicial" , $obRARRGrupoVencimento->getLimiteInicial() );
                            $this->obTARRGrupoVencimento->setDado( "limite_final"   , $obRARRGrupoVencimento->getLimiteFinal() );
                            $this->obTARRGrupoVencimento->setDado( "utilizar_unica" , $obRARRGrupoVencimento->getUtilizarCotaUnica() );

                            $obErro = $this->obTARRGrupoVencimento->inclusao( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                        } else {
                            $this->obTARRGrupoVencimento->setDado( "ano_exercicio"  , $this->getAnoExercicio() );
                            $this->obTARRGrupoVencimento->setDado( "cod_grupo"      , $this->getCodigoGrupo() );
                            $this->obTARRGrupoVencimento->setDado( "cod_vencimento" , $obRARRGrupoVencimento->getCodigoVencimento() );
                            $this->obTARRGrupoVencimento->setDado( "descricao"      , $obRARRGrupoVencimento->getDescricao() );
                            $this->obTARRGrupoVencimento->setDado( "data_vencimento_parcela_unica" , $obRARRGrupoVencimento->getVencimentoValorIntegral());
                            $this->obTARRGrupoVencimento->setDado( "limite_inicial" , $obRARRGrupoVencimento->getLimiteInicial() );
                            $this->obTARRGrupoVencimento->setDado( "limite_final"   , $obRARRGrupoVencimento->getLimiteFinal() );
                            $this->obTARRGrupoVencimento->setDado( "utilizar_unica" , $obRARRGrupoVencimento->getUtilizarCotaUnica() );
                            $obErro = $this->obTARRGrupoVencimento->alteracao( $boTransacao );

                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                            unset( $arGrupo[$inChave] );
                        }
                    }
                    if ( !$obErro->ocorreu() ) {
                        foreach ($arGrupo as $inChaveGrupo => $boValor) {
                            $arChave = explode(".",$inChaveGrupo);
                            $obRARRGrupoVencimento->roRARRCalendarioFiscal->setCodigoGrupo( $arChave[0] );
                            $obRARRGrupoVencimento->setCodigoVencimento( $arChave[1] );
                            $obErro = $obRARRGrupoVencimento->excluirDesconto( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            }

                            $obErro = $obRARRGrupoVencimento->excluirParcela( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            }

                            $this->obTARRGrupoVencimento->setDado( "cod_grupo"      , $arChave[0] );
                            $this->obTARRGrupoVencimento->setDado( "cod_vencimento" , $arChave[1] );
                            $this->obTARRGrupoVencimento->setDado( "descricao"      , $obRARRGrupoVencimento->getDescricao() );
                            $this->obTARRGrupoVencimento->setDado( "data_vencimento_parcela_unica" , $obRARRGrupoVencimento->getVencimentoValorIntegral());
                            $obErro = $this->obTARRGrupoVencimento->exclusao( $boTransacao );

                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                        }
                        if ( !$obErro->ocorreu() ) {
                            $arChaveAtributoCalendario =  array( "cod_grupo" => $this->getCodigoGrupo() );
                            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCalendario );
                            $obErro = $this->obRCadastroDinamico->alterarValores( $boTransacao );
                        }
                    }
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRCalendarioFiscal);

        return $obErro;
    }

    /**
        * Alterar celendario
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function excluirCalendario($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $arChaveAtributoCalendario =  array( "cod_grupo" => $this->getCodigoGrupo() );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCalendario );
            $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->roUltimoGrupoVencimento->excluirGrupoVencimento( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTARRCalendarioFiscal->setDado( "ano_exercicio" , $this->getAnoExercicio() );
                    $this->obTARRCalendarioFiscal->setDado( "cod_grupo" , $this->getCodigoGrupo() );
                    $obErro = $this->obTARRCalendarioFiscal->exclusao( $boTransacao );
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRCalendarioFiscal);

        return $obErro;
    }

    /**
        * Lista todos os grupo de creditos de acordo com o filtro
        * @access Public
        * @param RecordSet
        *        Transacao
        * @return RecordSet
    */
    public function recuperaGrupoCredito(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";

        if ( $this->getCodigoGrupo() ) {
            $stFiltro .= " AND GC.COD_GRUPO = ".$this->getCodigoGrupo();
        }

        $stOrdem = " ORDER BY GC.COD_GRUPO ";

        $obErro = $this->obTARRCalendarioFiscal->recuperaGrupoVencimentos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    //$this->obTARRCalendarioFiscal->debug();
        return $obErro;
    }

    /**
        * Lista todos os calendarios fiscais de acordo com o filtro
        * @access Public
        * @param RecordSet
        *        Transacao
        * @return RecordSet
    */
    public function listarCalendario(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";

        if ( $this->getCodigoGrupo() ) {
            $stFiltro .= " GC.COD_GRUPO = ".$this->getCodigoGrupo()." AND ";
        }

        if ( $this->getAnoExercicio() ) {
            $stFiltro .= " GC.ANO_EXERCICIO = '".$this->getAnoExercicio()."' AND ";
        }

        if ($stFiltro) {
            $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
        }

        $stOrdem = " ORDER BY GC.COD_GRUPO ";

        $obErro = $this->obTARRCalendarioFiscal->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    //$this->obTARRCalendarioFiscal->debug();
        return $obErro;
    }

    /**
        * Consulta calendario fiscal
        * @access Public
        * @param  Object obTransacao
        * @return Object obRARRCalendarioFiscal
    */
    public function consultarCalendario($boTransacao = "")
    {
        $obErro = $this->listarCalendario( $rsRecordSet, $boTransacao );

        if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
            $this->setValorMinimo( $rsRecordSet->getCampo( "valor_minimo" ) );
            $this->setValorMinimoParcela( $rsRecordSet->getCampo( "valor_minimo_parcela" ) );
            $this->setValorMinimoIntegral( $rsRecordSet->getCampo( "valor_minimo_lancamento" ) );
        }

        return $obErro;
    }
}
