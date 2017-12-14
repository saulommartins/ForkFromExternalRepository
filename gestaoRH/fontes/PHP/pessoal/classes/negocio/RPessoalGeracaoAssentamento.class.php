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
    * Classe de regra de negócio dos assentamentos por contrato servidor.
    * Data de Criação: 27/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name:  $
    $Author: souzadl $
    $Date: 2008-03-10 17:03:34 -0300 (Seg, 10 Mar 2008) $

    Caso de uso: uc-04.04.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalContratoServidor.class.php"                                 );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamento.class.php"                                     );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                         );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalVantagem.class.php"                                         );

class RPessoalGeracaoAssentamento
{
/**
   * @access Private
   * @var Integer
*/
var $inCodAssentamentoGerado;
/**
   * @access Private
   * @var Integer
*/
var $arCodNorma;
/**
   * @access Private
   * @var String
*/
var $stDescricaoObservacao;
/**
   * @access Private
   * @var String
*/
var $stDescricaoExclusao;
/**
   * @access Private
   * @var String
*/
var $stTimestamp;
/**
   * @access Private
   * @var Boolean
*/
var $boAutomatico;
/**
   * @access Private
   * @var Date
*/
var $dtPeriodoInicial;
/**
   * @access Private
   * @var Date
*/
var $dtPeriodoFinal;
/**
   * @access Private
   * @var Date
*/
var $dtPeriodoLicPremioInicial;
/**
   * @access Private
   * @var Date
*/
var $dtPeriodoLicPremioFinal;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalContratoServidor;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalAssentamento;

var $inCodTipoClassificacao;
 
/**
    * @access Public
    * @param Integer $valor
*/
function setCodAssentamentoGerado($valor) { $this->inCodAssentamentoGerado     = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodNorma($valor) { $this->arCodNorma     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricaoObservacao($valor) { $this->stDescricaoObservacao       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricaoExclusao($valor) { $this->stDescricaoExclusao         = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTimestamp($valor) { $this->stTimestamp         = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setAutomatico($valor) { $this->boAutomatico                = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setPeriodoInicial($valor) { $this->dtPeriodoInicial            = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setPeriodoFinal($valor) { $this->dtPeriodoFinal              = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setPeriodoLicPremioInicial($valor) { $this->dtPeriodoLicPremioInicial            = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setPeriodoLicPremioFinal($valor) { $this->dtPeriodoLicPremioFinal              = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalContratoServidor($valor) { $this->obRPessoalContratoServidor  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalAssentamento($valor) { $this->obRPessoalAssentamento      = $valor; }


/**
    * @access Public
    * @param Integer $valor
*/
function setCodTipoClassificacao ($valor) { $this->inCodTipoClassificacao      = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodAssentamentoGerado() { return $this->inCodAssentamentoGerado;     }
/**
    * @access Public
    * @return Integer
*/
function getCodNorma() { return $this->arCodNorma;     }
/**
    * @access Public
    * @return String
*/
function getDescricaoObservacao() { return $this->stDescricaoObservacao;       }
/**
    * @access Public
    * @return String
*/
function getDescricaoExclusao() { return $this->stDescricaoExclusao;         }
/**
    * @access Public
    * @return String
*/
function getTimestamp() { return $this->stTimestamp;         }
/**
    * @access Public
    * @return Boolean
*/
function getAutomatico() { return $this->boAutomatico;                }
/**
    * @access Public
    * @return Date
*/
function getPeriodoInicial() { return $this->dtPeriodoInicial;            }
/**
    * @access Public
    * @return Date
*/
function getPeriodoFinal() { return $this->dtPeriodoFinal;              }
/**
    * @access Public
    * @return Date
*/
function getPeriodoLicPremioInicial() { return $this->dtPeriodoLicPremioInicial;            }
/**
    * @access Public
    * @return Date
*/
function getPeriodoLicPremioFinal() { return $this->dtPeriodoLicPremioFinal;              }
/**
    * @access Public
    * @return Object
*/
function getRPessoalContratoServidor() { return $this->obRPessoalContratoServidor;  }
/**
    * @access Public
    * @return Object
*/
function getRPessoalAssentamento() { return $this->obRPessoalAssentamento;      }

/**
    * @access Public
    * @return Integer
*/
function getCodTipoClassificacao () { return $this->inCodTipoClassificacao; }

/**
    * Método construtor
    * @access Private
*/
function RPessoalGeracaoAssentamento()
{
    $this->setRPessoalContratoServidor( new RPessoalContratoServidor(new RPessoalServidor) );
    $this->setRPessoalAssentamento    ( new RPessoalAssentamento( new RPessoalVantagem )   );
}

/**
    * Inclui dados do assentamento gerado no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirGeracaoAssentamento($boTransacao = "")
{
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoContratoServidor.class.php" );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php" );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoNorma.class.php" );
    include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalRegistrarEventoPorAssentamento.class.php" );

    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arFiltros['validacao'] = 'validar';
        $arFiltros['inCodContrato'] = $this->obRPessoalContratoServidor->getCodContrato();
        $arFiltros['inCodAssentamento'] = $this->obRPessoalAssentamento->getCodAssentamento();
        $arFiltros['dtPeriodoInicial'] = $this->getPeriodoInicial();
        $arFiltros['dtPeriodoFinal'] = $this->getPeriodoFinal();
        $arFiltros['inCodTipoClassificacao'] = $this->getCodTipoClassificacao();
        $obErro = $this->listarAssentamentoServidor($rsAssentamento,$arFiltros,$stOrdem,$boTransacao);
        if ( $rsAssentamento->getNumLinhas() > 0 ) {
            $obErro->setDescricao("Período do assentamento (".$this->getPeriodoInicial()." a ".$this->getPeriodoFinal().") colide com outro lançamento já efetuado para a matrícula.");
        }
        if ( !$obErro->ocorreu() and $rsAssentamento->getNumLinhas() < 0 ) {
            $obTPessoalAssentamentoGeradoContratoServidor = new TPessoalAssentamentoGeradoContratoServidor;
            $obErro = $obTPessoalAssentamentoGeradoContratoServidor->proximoCod( $this->inCodAssentamentoGerado, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTPessoalAssentamentoGeradoContratoServidor->setDado( "cod_assentamento_gerado" , $this->getCodAssentamentoGerado() );
                $obTPessoalAssentamentoGeradoContratoServidor->setDado( "cod_contrato"            , $this->obRPessoalContratoServidor->getCodContrato() );
                $obErro = $obTPessoalAssentamentoGeradoContratoServidor->inclusao( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                $obTPessoalAssentamentoGerado = new TPessoalAssentamentoGerado;
                $obErro = $obTPessoalAssentamentoGerado->recuperaNow3($stTimestamp,$boTransacao);
            }
            if ( !$obErro->ocorreu() ) {
                $obTPessoalAssentamentoGerado->setDado( "cod_assentamento_gerado" , $this->getCodAssentamentoGerado()                   );
                $obTPessoalAssentamentoGerado->setDado( "timestamp"               , $stTimestamp                   );
                $obTPessoalAssentamentoGerado->setDado( "cod_assentamento"        , $this->obRPessoalAssentamento->getCodAssentamento() );
                $obTPessoalAssentamentoGerado->setDado( "periodo_inicial"         , $this->getPeriodoInicial()                          );
                $obTPessoalAssentamentoGerado->setDado( "periodo_final"           , $this->getPeriodoFinal()                            );
                $obTPessoalAssentamentoGerado->setDado( "automatico"              , $this->getAutomatico()                              );
                $obTPessoalAssentamentoGerado->setDado( "observacao"              , $this->getDescricaoObservacao()                     );
                $obErro = $obTPessoalAssentamentoGerado->inclusao( $boTransacao );
            }
            if ( !$obErro->ocorreu() AND $this->getCodNorma() != "") {
                $obTPessoalAssentamentoGeradoNorma = new TPessoalAssentamentoGeradoNorma();                
                if (is_array($this->getCodNorma())) {
                    foreach ($this->getCodNorma() as $arNormas) {
                        $obTPessoalAssentamentoGeradoNorma->setDado( "cod_assentamento_gerado"  , $this->getCodAssentamentoGerado() );
                        $obTPessoalAssentamentoGeradoNorma->setDado( "timestamp"                , $stTimestamp                      );
                        $obTPessoalAssentamentoGeradoNorma->setDado( "cod_norma"                , $arNormas['inCodNorma']           );
                        $obErro = $obTPessoalAssentamentoGeradoNorma->inclusao( $boTransacao );
                    }
                }
            }
            if ( !$obErro->ocorreu() and $this->getPeriodoLicPremioInicial() != "" and $this->getPeriodoLicPremioFinal() != "") {
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoLicencaPremio.class.php");
                $obTPessoalAssentamentoLicencaPremio = new TPessoalAssentamentoLicencaPremio();
                $obTPessoalAssentamentoLicencaPremio->setDado("dt_inicial",$this->getPeriodoLicPremioInicial());
                $obTPessoalAssentamentoLicencaPremio->setDado("dt_final",$this->getPeriodoLicPremioFinal());
                $obTPessoalAssentamentoLicencaPremio->setDado("cod_assentamento_gerado",$this->getCodAssentamentoGerado());
                $obTPessoalAssentamentoLicencaPremio->setDado("timestamp",$stTimestamp);
                $obErro = $obTPessoalAssentamentoLicencaPremio->inclusao($boTransacao);
            }
            if ( !$obErro->ocorreu() ) {
                $obFPessoalRegistrarEventoPorAssentamento = new FPessoalRegistrarEventoPorAssentamento;
                $obFPessoalRegistrarEventoPorAssentamento->setDado("cod_contrato"       ,$this->obRPessoalContratoServidor->getCodContrato());
                $obFPessoalRegistrarEventoPorAssentamento->setDado("cod_assentamento"   ,$this->obRPessoalAssentamento->getCodAssentamento());
                $obFPessoalRegistrarEventoPorAssentamento->setDado("acao","incluir");
                $obErro = $obFPessoalRegistrarEventoPorAssentamento->registrarEventoPorAssentamento($boTransacao);
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalAssentamentoGeradoContratoServidor );

    return $obErro;
}

/**
    * altera dados do assentamento gerado no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarGeracaoAssentamento($boTransacao = "")
{
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoContratoServidor.class.php" );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php" );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoNorma.class.php" );
    include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalRegistrarEventoPorAssentamento.class.php" );
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arFiltros['validacao'] = 'validar';
        $arFiltros['inCodContrato']     = $this->obRPessoalContratoServidor->getCodContrato();
        $arFiltros['inCodAssentamento'] = $this->obRPessoalAssentamento->getCodAssentamento();
        $arFiltros['dtPeriodoInicial']  = $this->getPeriodoInicial();
        $arFiltros['dtPeriodoFinal']    = $this->getPeriodoFinal();
        $obErro = $this->listarAssentamentoServidor($rsAssentamento,$arFiltros,$stOrdem,$boTransacao);
        if ( $rsAssentamento->getNumLinhas() > 0 and $rsAssentamento->getCampo('cod_assentamento_gerado') != $this->getCodAssentamentoGerado() ) {
            $stDescricaoAssentamento  = $rsAssentamento->getCampo("descricao_assentamento");
            $stDescricaoClassificacao = $rsAssentamento->getCampo("descricao_classificacao");
            $stComplemento            = "contrato(".$rsAssentamento->getCampo('registro').")";
            $stMensagem = "Este período(".$arFiltros['dtPeriodoInicial']." até ".$arFiltros['dtPeriodoFinal'].") já foi cadastrado para o $stComplemento, classifiação($stDescricaoClassificacao) e assentamento($stDescricaoAssentamento).";
            $obErro->setDescricao($stMensagem);
        }
        if ( !$obErro->ocorreu() ) {
            $obTPessoalAssentamentoGerado = new TPessoalAssentamentoGerado;
            $stFiltroGerado = " AND assentamento_gerado.cod_assentamento_gerado = ".$this->getCodAssentamentoGerado();
            $obErro = $obTPessoalAssentamentoGerado->recuperaAssentamentoGerado($rsAssentamentoGerado,$stFiltroGerado,"",$boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obTPessoalAssentamentoGerado->recuperaNow3($stTimestamp,$boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            $obTPessoalAssentamentoGerado->setDado( "cod_assentamento_gerado" , $this->getCodAssentamentoGerado()                   );
            $obTPessoalAssentamentoGerado->setDado( "timestamp"               , $stTimestamp                   );
            $obTPessoalAssentamentoGerado->setDado( "cod_assentamento"        , $this->obRPessoalAssentamento->getCodAssentamento() );
            $obTPessoalAssentamentoGerado->setDado( "periodo_inicial"         , $this->getPeriodoInicial()                          );
            $obTPessoalAssentamentoGerado->setDado( "periodo_final"           , $this->getPeriodoFinal()                            );
            $obTPessoalAssentamentoGerado->setDado( "automatico"              , $this->getAutomatico()                              );
            $obTPessoalAssentamentoGerado->setDado( "observacao"              , $this->getDescricaoObservacao()                     );
            $obErro = $obTPessoalAssentamentoGerado->inclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() AND $this->getCodNorma() != "") {
                $obTPessoalAssentamentoGeradoNorma = new TPessoalAssentamentoGeradoNorma();
                foreach ($this->getCodNorma() as $arNormas) {
                    $obTPessoalAssentamentoGeradoNorma->setDado( "cod_assentamento_gerado" , $this->getCodAssentamentoGerado()                   );
                    $obTPessoalAssentamentoGeradoNorma->setDado( "timestamp"               , $stTimestamp                   );
                    $obTPessoalAssentamentoGeradoNorma->setDado( "cod_norma"               , $arNormas);
                    $obErro = $obTPessoalAssentamentoGeradoNorma->inclusao( $boTransacao );
                }
        }
        if ( !$obErro->ocorreu() and $this->getPeriodoLicPremioInicial() != "" and $this->getPeriodoLicPremioFinal() != "") {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoLicencaPremio.class.php");
            $obTPessoalAssentamentoLicencaPremio = new TPessoalAssentamentoLicencaPremio();
            $obTPessoalAssentamentoLicencaPremio->setDado("dt_inicial",$this->getPeriodoLicPremioInicial());
            $obTPessoalAssentamentoLicencaPremio->setDado("dt_final",$this->getPeriodoLicPremioFinal());
            $obTPessoalAssentamentoLicencaPremio->setDado("cod_assentamento_gerado",$this->getCodAssentamentoGerado());
            $obTPessoalAssentamentoLicencaPremio->setDado("timestamp",$stTimestamp);
            $obErro = $obTPessoalAssentamentoLicencaPremio->inclusao($boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            $obFPessoalRegistrarEventoPorAssentamento = new FPessoalRegistrarEventoPorAssentamento;
            $obFPessoalRegistrarEventoPorAssentamento->setDado("cod_contrato"       ,$this->obRPessoalContratoServidor->getCodContrato());
            $obFPessoalRegistrarEventoPorAssentamento->setDado("cod_assentamento"   ,$this->obRPessoalAssentamento->getCodAssentamento());
            $obFPessoalRegistrarEventoPorAssentamento->setDado("acao","incluir");
            $obErro = $obFPessoalRegistrarEventoPorAssentamento->registrarEventoPorAssentamento($boTransacao);
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalAssentamentoGeradoContratoServidor );

    return $obErro;
}

/**
    * exclui dados do assentamento gerado no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirGeracaoAssentamento($boTransacao = "")
{
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoExcluido.class.php" );
    include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalRegistrarEventoPorAssentamento.class.php" );
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obFPessoalRegistrarEventoPorAssentamento = new FPessoalRegistrarEventoPorAssentamento();
        $obFPessoalRegistrarEventoPorAssentamento->setDado("cod_contrato"       ,$this->obRPessoalContratoServidor->getCodContrato());
        $obFPessoalRegistrarEventoPorAssentamento->setDado("cod_assentamento"   ,$this->obRPessoalAssentamento->getCodAssentamento());
        $obFPessoalRegistrarEventoPorAssentamento->setDado("acao","excluir");
        $obErro = $obFPessoalRegistrarEventoPorAssentamento->registrarEventoPorAssentamento($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalAssentamentoGeradoExcluido = new TPessoalAssentamentoGeradoExcluido;
        $obTPessoalAssentamentoGeradoExcluido->setDado( "cod_assentamento_gerado" , $this->getCodAssentamentoGerado()  );
        $obTPessoalAssentamentoGeradoExcluido->setDado( "timestamp"               , $this->getTimestamp()  );
        $obTPessoalAssentamentoGeradoExcluido->setDado( "descricao"               , $this->getDescricaoExclusao()      );
        $obErro = $obTPessoalAssentamentoGeradoExcluido->inclusao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalAssentamentoGeradoContratoServidor );

    return $obErro;
}

/**
    * Lista os assentamentos gerados
    * @access Private
    * @param  Object $obTransacao, $stFiltro, $stOrdem, $obTransacao
    * @return Object Objeto Erro
*/
function listar(&$rsAssentamentos, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php" );
    $obTPessoalAssentamentoGerado = new TPessoalAssentamentoGerado;
    $obErro = $obTPessoalAssentamentoGerado->recuperaRelacionamento( $rsAssentamentos, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os assentamentos gerados
    * @access Private
    * @param  Object $obTransacao, $stFiltro, $stOrdem, $obTransacao
    * @return Object Objeto Erro
*/
function listarAssentamentoServidor( &$rsAssentamentos, $arFiltros=array(), $stOrdem = "", $boTransacao = "" )
{
    if ($arFiltros['inCodContrato'] != "") {
        $stFiltro .= " AND assentamento_gerado_contrato_servidor.cod_contrato = ".$arFiltros['inCodContrato'];
    }
    if ($arFiltros['inRegistro'] != "") {
        $stFiltro .= " AND contrato.registro = ".$arFiltros['inRegistro'];
    }
    
    if ( $arFiltros['validacao'] == 'validar' ) {
        $stFiltro .= "AND 1 = (SELECT 1
              FROM pessoal.tipo_classificacao
             WHERE cod_tipo not in (1,4)
               AND cod_tipo = (SELECT tipo_classificacao.cod_tipo
                                 FROM pessoal.assentamento_assentamento
                                 JOIN pessoal.classificacao_assentamento
                                   ON classificacao_assentamento.cod_classificacao = assentamento_assentamento.cod_classificacao
                                 JOIN pessoal.tipo_classificacao
                                   ON tipo_classificacao.cod_tipo = classificacao_assentamento.cod_tipo
                                WHERE assentamento_assentamento.cod_assentamento = ".$arFiltros['inCodAssentamento'].")
           ) \n";
    } else if ($arFiltros['inCodAssentamento'] != "") {
        $stFiltro .= " AND assentamento_assentamento.cod_assentamento = ".$arFiltros['inCodAssentamento']." \n";
    }
    if ($arFiltros['inCodClassificacao'] != "") {
        $stFiltro .= " AND assentamento_assentamento.cod_classificacao = ".$arFiltros['inCodClassificacao'];
    }
    if ($arFiltros['dtPeriodoInicial'] != "" and $arFiltros['dtPeriodoFinal'] != "") {
        $stFiltro .= " AND ((to_date('".$arFiltros['dtPeriodoInicial']."','dd/mm/yyyy') BETWEEN periodo_inicial AND periodo_final) OR
                           (to_date('".$arFiltros['dtPeriodoFinal']."','dd/mm/yyyy') BETWEEN periodo_inicial AND periodo_final))";
    }
    if ($arFiltros['dtPeriodoInicial'] != "" and $arFiltros['dtPeriodoFinal'] == "") {
        $stFiltro .= " AND to_date('".$arFiltros['dtPeriodoInicial']."','dd/mm/yyyy') BETWEEN periodo_inicial AND periodo_final";
    }
    if ($arFiltros['dtPeriodoInicial2'] != "" and $arFiltros['dtPeriodoFinal2'] != "") {
        $stFiltro .= " AND ((periodo_inicial BETWEEN to_date('".$arFiltros['dtPeriodoInicial2']."','dd/mm/yyyy') AND to_date('".$arFiltros['dtPeriodoFinal2']."','dd/mm/yyyy')) OR
                          (periodo_final   BETWEEN to_date('".$arFiltros['dtPeriodoInicial2']."','dd/mm/yyyy') AND to_date('".$arFiltros['dtPeriodoFinal2']."','dd/mm/yyyy')))";
    }
    if ($arFiltros['inCodCargo'] != "") {
        $stFiltro .= " AND contrato.cod_cargo = ".$arFiltros['inCodCargo'];
    }
    if ($arFiltros['inCodEspecialidade'] != "") {
        $stFiltro .= " AND contrato.cod_especialidade = ".$arFiltros['inCodEspecialidade'];
    }
    if ($arFiltros['inCodFuncao'] != "") {
        $stFiltro .= " AND contrato.cod_funcao = ".$arFiltros['inCodFuncao'];
    }
    if ($arFiltros['inCodEspecialidadeFuncao'] != "") {
        $stFiltro .= " AND contrato.cod_especialidade_funcao = ".$arFiltros['inCodEspecialidadeFuncao'];
    }
    if ($arFiltros['inCodLotacao'] != "") {
        $stFiltro .= " AND cod_estrutural = '".$arFiltros['inCodLotacao']."'";
    }
    if ($arFiltros['inCodAssentamentoGerado'] != "") {
        $stFiltro .= " AND assentamento_gerado.cod_assentamento_gerado = '".$arFiltros['inCodAssentamentoGerado']."'";
    }
    if ($arFiltros['inCodTipoClassificacao'] != "") {
        $stFiltro .= " AND classificacao_assentamento.cod_tipo = ".$arFiltros['inCodTipoClassificacao']." ";
    }
    if ($stOrdem == "") {
        $stOrdem = " nom_cgm,cod_contrato";
    }
    $obErro = $this->listar( $rsAssentamentos, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

}
?>
