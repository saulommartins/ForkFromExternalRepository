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
    * Classe de Regra de Negócio ContabilidadeConfiguracao
    * Data de Criação   : 03/11/2004

    * @author Analista : Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Regra

    * $Id: RContabilidadeConfiguracao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.01 , uc-02.02.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"  		       );

class RContabilidadeConfiguracao
{

    /**
        * @var Object
        * @access Private
    */
    public $obTransacao;
    /**
        * @var Integer
        * @access Private
    */
    public $inCodModulo;
    /**
        * @var Date
        * @access Private
    */
    public $dtDataImplantacao;
    /**
        * @var String
        * @access Private
    */
    public $stMascaraPlanoContas;
    /**
        * @var Integer
        * @access Private
    */
    public $inMesCorrente;
    /**
        * @var String
        * @access Private
    */
    public $stExercicio;

    /**
        * @var String
        * @access Private
    */
    public $stParDataImplantacao;
    /**
        * @var String
        * @access Private
    */
    public $stParMascaraPlanoContas;
    /**
        * @var String
        * @access Private
    */
    public $stParMesCorrente;
    /**
        * @var String
        * @access Private
    */
    public $stParDiarioUltimaPagina;
    /**
        * @var String
        * @access Private
    */
    public $stDiarioUltimaPagina;
    /**
        * @var String
        * @access Private
    */
    public $stParDiarioUltimaPaginaExercicio;
    /**
        * @var String
        * @access Private
    */
    public $stParUtilizarEncerramentoMes;
    /**
        * @var String
        * @access Private
    */
    public $stDiarioUltimaPaginaExercicio;
    /**
        * @var Boolean
        * @access Private
    */
    public $boUtilizarEncerramentoMes;

    /**
         * @access Public
         * @param Object $valor
    */
    public function setTransacao($valor) { $this->obTransacao                   = $valor; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function setCodModulo($valor) { $this->inCodModulo                   = $valor; }
    /**
         * @access Public
         * @param Date $valor
    */
    public function setDataImplantacao($valor) { $this->dtDataImplantacao         	 = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setMascaraPlanoContas($valor) { $this->stMascaraPlanoContas       	 = $valor; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function setMescorrente($valor) { $this->inMesCorrente        		 = $valor; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function setUtilizarEncerramentoMes($valor) { $this->boUtilizarEncerramentoMes     = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setExercicio($valor) { $this->stExercicio       	        = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setParDataImplantacao($valor) { $this->stParDataImplantacao         	 = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setParMascaraPlanoContas($valor) { $this->stParMascaraPlanoContas       	 = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setParMesCorrente($valor) { $this->stParMesCorrente        		 = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setParUtilizarEncerramentoMes($valor) { $this->stParUtilizarEncerramentoMes  = $valor; }

    /**
         * @access Public
         * @param Object $valor
    */
    public function getTransacao() { return $this->obTransacao;                   }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function getCodModulo() { return $this->inCodModulo;                   }
    /**
         * @access Public
         * @param Date $valor
    */
    public function getDataImplantacao() { return $this->dtDataImplantacao;                }
    /**
         * @access Public
         * @param String $valor
    */
    public function getMascaraPlanoContas() { return $this->stMascaraPlanoContas;			 	}
    /**
         * @access Public
         * @param Integer $valor
    */
    public function getMesCorrente() { return $this->inMesCorrente;                    }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function getUtilizarEncerramentoMes() { return $this->boUtilizarEncerramentoMes;        }
    /**
         * @access Public
         * @param String $valor
    */
    public function getExercicio() { return $this->stExercicio;	        		 	}
    /**
         * @access Public
         * @param String $valor
    */
    public function getParDataImplantacao() { return $this->stParDataImplantacao;             }
    /**
         * @access Public
         * @param String $valor
    */
    public function getParMascaraPlanoContas() { return $this->stParMascaraPlanoContas;		}
    /**
         * @access Public
         * @param String $valor
    */
    public function getParMesCorrente() { return $this->stParMesCorrente;              }
    /**
         * @access Public
         * @param String $valor
    */
    public function getParUtilizarEncerramentoMes() { return $this->stParUtilizarEncerramentoMes;  }
    /**
         * @access Public
         * @param String $valor
    */
    public function getDiarioUltimaPagina() { return $this->stDiarioUltimaPagina;          }
    /**
         * @access Public
         * @param String $valor
    */
    public function getDiarioUltimaPaginaExercicio() { return $this->stDiarioUltimaPaginaExercicio; }

    public function RContabilidadeConfiguracao()
    {
        $this->setExercicio              	    ( Sessao::getExercicio()             );
        $this->setTransacao              	    ( new Transacao                  );
        $this->setParDataImplantacao            ( "data_implantacao"   	         );
        $this->setParMascaraPlanoContas   	    ( "masc_plano_contas"    		 );
        $this->setParMesCorrente   	 		    ( "mes_processamento"  	         );
        $this->setParUtilizarEncerramentoMes    ( "utilizar_encerramento_mes"    );
        $this->setCodModulo   	 		 	    ( "9"				  			 );
        $this->stParDiarioUltimaPagina 	        = "diario_ultima_pagina";
        $this->stParDiarioUltimaPaginaExercicio = "diario_ultima_pagina_exercicio";
    }

    public function salvar($boTransacao = "")
    {
        include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
        $obTAdministracaoConfiguracao   = new TAdministracaoConfiguracao;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo() );
            $obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio() );

            $obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParDataImplantacao() );
            $arDataTmp = explode( '/',$this->getDataImplantacao() );
            if ( date("Ymd", mktime(0,0,0,$arDataTmp[1],$arDataTmp[0],$arDataTmp[2])) <= date("Ymd") ) {
                $obTAdministracaoConfiguracao->setDado( "valor"     , $this->getDataImplantacao() );
                $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParMascaraPlanoContas()    );
                    $obTAdministracaoConfiguracao->setDado( "valor"     , $this->getMascaraPlanoContas() );
                    $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParMesCorrente()    );
                        $obTAdministracaoConfiguracao->setDado( "valor"     , $this->getMesCorrente() );
                        $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParUtilizarEncerramentoMes()    );
                            $obTAdministracaoConfiguracao->setDado( "valor"     , $this->getUtilizarEncerramentoMes() );
                            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
                        }
                    }
                }
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
            } else {
                $obErro->setDescricao( "A data digitada é maior que a data de hoje" );
            }
        }

        return $obErro;
    }

    public function consultar($boTransacao = "")
    {
        include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
        $obTAdministracaoConfiguracao   = new TAdministracaoConfiguracao;

        $obErro = $this->buscaModulo( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo()            );
            $obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio()            );
            $obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParDataImplantacao() );
            $obErro = $obTAdministracaoConfiguracao->recuperaPorChave( $rsDataImplantacao   , $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->setDataImplantacao( $rsDataImplantacao->getCampo( "valor" ) );
                $obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParMascaraPlanoContas() );
                $obErro = $obTAdministracaoConfiguracao->recuperaPorChave( $rsMascaraPlanoContas, $boTransacao );
                $this->setMascaraPlanoContas( $rsMascaraPlanoContas->getCampo( "valor" ) );
                if ( !$obErro->ocorreu() ) {
                    $obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParMesCorrente() );
                    $obErro = $obTAdministracaoConfiguracao->recuperaPorChave( $rsMesCorrente, $boTransacao );
                    $this->setMescorrente( $rsMesCorrente->getCampo( "valor" ) );
                    if ( !$obErro->ocorreu() ) {
                        $obTAdministracaoConfiguracao->setDado( "parametro" , $this->stParDiarioUltimaPagina );
                        $obErro = $obTAdministracaoConfiguracao->recuperaPorChave( $rsUltimaPagina, $boTransacao );
                        $this->stDiarioUltimaPagina = $rsUltimaPagina->getCampo( "valor" );
                        if ( !$obErro->ocorreu() ) {
                            $obTAdministracaoConfiguracao->setDado( "parametro" , $this->stParDiarioUltimaPaginaExercicio );
                            $obErro = $obTAdministracaoConfiguracao->recuperaPorChave( $rsUltimaPaginaExercicio, $boTransacao );
                            $this->stDiarioUltimaPaginaExercicio = $rsUltimaPaginaExercicio->getCampo( "valor" );
                            if ( !$obErro->ocorreu() ) {
                                $obTAdministracaoConfiguracao->setDado( "parametro" , $this->stParUtilizarEncerramentoMes );
                                $obErro = $obTAdministracaoConfiguracao->recuperaPorChave( $rsUtilizarEncerramentoMes, $boTransacao );
                                $this->boUtilizarEncerramentoMes = $rsUtilizarEncerramentoMes->getCampo( "valor" );
                            }
                        }
                    }
                }
            }
        }

        return $obErro;
    }

    public function buscaModulo($boTransacao = "")
    {
        include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php"         );
        $obTAcao                    = new TAdministracaoAcao;

        ;
        $stFiltro  = " AND A.cod_acao = ".Sessao::read('acao')." ";
        $obErro = $obTAcao->recuperaRelacionamento( $rsRelacionamento, $stFiltro, "", $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setCodModulo( $rsRelacionamento->getCampo("cod_modulo") );
        }

        return $obErro;
    }

}
