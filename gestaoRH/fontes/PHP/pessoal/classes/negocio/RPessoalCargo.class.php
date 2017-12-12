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
    * Classe de regra de negócio para Pessoal-Cargo
    * Data de Criação: 07/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Gustavo Passos Tourinho
    * @author Desenvolvedor: Vandre Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Revision: 32866 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-15 15:00:34 -0300 (Sex, 15 Jun 2007) $

    Caso de uso: uc-04.04.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCargo.class.php"               );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCargoPadrao.class.php"         );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCbo.class.php"                 );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCboCargo.class.php"            );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAtributoCargoValor.class.php"  );

include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEventoCasoCargo.class.php"  );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEventoCasoEspecialidade.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidade.class.php"       );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidadePadrao.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCboEspecialidade.class.php"    );

include_once ( CAM_GRH_PES_NEGOCIO."RPessoalSubDivisao.class.php"             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php"          );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCargoSubDivisao.class.php"        );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalEspecialidade.class.php"          );
include_once ( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php"                       );
include_once ( CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php"           );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"               );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFeriasFuncao.class.php"    );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCargoRequisito.class.php"      );
include_once '../../../../../../gestaoRH/fontes/PHP/folhaPagamento/classes/mapeamento/TFolhaPagamentoConfiguracaoEventoCaso.class.php';

/**
    * Classe de regra de negócio para Pessoal-Cargo
    * Data de Criação: 02/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Gustavo Passos Tourinho
    * @author Desenvolvedor: Vandre Miguel Ramos

    * @package URBEM
    * @subpackage Regra
*/
class RPessoalCargo
{
/**
    * @access Private
    * @var Integer
*/
var $inCodCargo;

/**
    * @access Private
    * @var String
*/
var $stDescricao;

/**
    * @access Private
    * @var integer
*/
var $inCBO;

/**
    * @access Private
    * @var boolean
*/
var $boCargoCC;

/**
    * @access Private
    * @var boolean
*/
var $boFuncao;

/**
    * @access Private
    * @var integer
*/
var $inCodEscolaridade;

/**
    * @access Private
    * @var text
*/
var $stAtribuicoes;

/**
    * @access Private
    * @var boolean
*/
var $boEspecialidade;

/**
    * @access Private
    * @var boolean
*/
var $boBuscarCargosNormasVencidas;

/**
    * @access Private
    * @var boolean
*/
var $inCodMes;

/**
    * @access Private
    * @var boolean
*/
var $stExercicio;

/**
    * @access Private
    * @var Object
*/
var $obRPadrao;

/**
    * @access Private
    * @var Object
*/
var $obRNorma;

/**
    * @access Private
    * @var array
*/
var $arRPessoalCargoSubDivisao;

/**
    * @access Private
    * @var array
*/
var $arRPessoalEspecialidade;

/**
    * @access Private
    * @var Object
*/
var $roUltimoCargoSubDivisao;

/**
    * @access Private
    * @var Object
*/
var $roUltimoEspecialidade;

/**
    * @access Private
    * @var Object
*/
var $obRConfiguracaoPessoal;
/**
    * @access Private
    * @var Object
*/
var $obRCadastroDinamico;

/**
    * @access Private
    * @var Array
*/
var $arCodRequisitos;

/**
    * @access Private
    * @var Object
*/
var $obTPessoalCargoRequisito;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodCargo($valor) { $this->inCodCargo    = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao   = $valor; }

/**
    * @access Public
    * @param integer $valor
*/
function setCBO($valor) { $this->inCBO         = $valor; }

/**
    * @access Public
    * @param boolean $valor
*/
function setCargo($valor) { $this->boCargoCC     = $valor; }

/**
    * @access Public
    * @param boolean $valor
*/
function setFuncao($valor) { $this->boFuncao      = $valor; }

/**
    * @access Public
    * @param integer $valor
*/
function setCodEscolaridade($valor) { $this->inCodEscolaridade = $valor; }

/**
    * @access Public
    * @param string $valor
*/
function setAtribuicoes($valor) { $this->stAtribuicoes = $valor; }

/**
    * @access Public
    * @param boolean $valor
*/
function setEspecialidade($valor) { $this->boEspecialidade = $valor; }

/**
    * @access Public
    * @param array $valor
*/
function setArRPessoalEspecialidade($valor) { $this->arRPessoalEspecialidade = $valor; }

/**
    * @access Public
    * @param array $valor
*/
function setBuscarCargosNormasVencidas($valor) { $this->boBuscarCargosNormasVencidas = $valor; }

/**
    * @access Public
    * @param array $valor
*/
function setCodRequisitos($valor) { $this->arCodRequisitos = $valor; }

/**
    * @access Public
    * @param array $valor
*/
function setCodMes($valor) { $this->inCodMes = $valor; }

/**
    * @access Public
    * @param array $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodCargo() { return $this->inCodCargo;  }

/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao; }

/**
    * @access Public
    * @return Integer
*/
function getCBO() { return $this->inCBO;    }

/**
    * @access Public
    * @return boolean
*/
function getCargo() { return $this->boCargoCC;   }

/**
    * @access Public
    * @return boolean
*/
function getFuncao() { return $this->boFuncao;    }

/**
    * @access Public
    * @return integer
*/
function getCodEscolaridade() { return $this->inCodEscolaridade; }

/**
    * @access Public
    * @return integer
*/
function getAtribuicoes() { return $this->stAtribuicoes; }

/**
    * @access Public
    * @return boolean
*/
function getEspecialidade() { return $this->boEspecialidade; }

/**
    * @access Public
    * @return boolean
*/
function getBuscarCargosNormasVencidas() { return $this->boBuscarCargosNormasVencidas; }

/**
    * @access Public
    * @return boolean
*/
function getCodMes() { return $this->inCodMes; }

/**
    * @access Public
    * @return boolean
*/
function getExercicio() { return $this->stExercicio; }

/**
    * @access Public
    * @return Array
*/
function getCodRequisitos() { return $this->arCodRequisitos; }

/**
     * Método construtor
     * @access Private
*/
function RPessoalCargo()
{
    $this->obTPessoalCargo           = new TPessoalCargo;
    $this->obTPessoalCargoPadrao     = new TPessoalCargoPadrao;
    $this->obRNorma                  = new RNorma;
    $this->obRFolhaPagamentoPadrao   = new RFolhaPagamentoPadrao;
    $this->obTransacao               = new Transacao;
    $this->obTPessoalSubDivisao      = new TPessoalSubDivisao;
    $this->obTessoalCargoSubDivisao  = new TPessoalCargoSubDivisao;
    $this->obRConfiguracaoPessoal    = new RConfiguracaoPessoal;
    //$this->obTPessoalCbo             = new TPessoalCbo;
    $this->obTPessoalCboCargo        = new TPessoalCboCargo;
    $this->obTPessoalCargoRequisito  = new TPessoalCargoRequisito;
    $this->arRPessoalCargoSubDivisao = array ();
    $this->arRPessoalEspecialidade   = array ();
    $this->obRCadastroDinamico = new RCadastroDinamico();
    $this->obRCadastroDinamico->setPersistenteValores   ( new TPessoalAtributoCargoValor );
    $this->obRCadastroDinamico->setCodCadastro(4);
    $this->obRCadastroDinamico->obRModulo->setCodModulo(22);
    $this->boBuscarCargosNormasVencidas = false;
    $this->obTFolhaPagamentoConfiguracaoEventoCaso = new TFolhaPagamentoConfiguracaoEventoCaso;
}

/**
    * Inclui os dados do cargo
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirCargo($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTPessoalCargo->proximoCod ( $this->inCodCargo, $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalCargo->setDado ( "cod_cargo"         , $this->getCodCargo()        );
        $this->obTPessoalCargo->setDado ( "descricao"         , $this->getDescricao()       );
        $this->obTPessoalCargo->setDado ( "cargo_cc"          , $this->getCargo()           );
        $this->obTPessoalCargo->setDado ( "funcao_gratificada", $this->getFuncao()          );
        $this->obTPessoalCargo->setDado ( "cod_escolaridade"  , $this->getCodEscolaridade() );
        $this->obTPessoalCargo->setDado ( "atribuicoes"       , $this->getAtribuicoes()     );
        $obErro = $this->obTPessoalCargo->inclusao ( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        // Se NÃO tem especialidade
        if (empty($this->arRPessoalEspecialidade) ) {
            $this->obRFolhaPagamentoPadrao->listarPadrao( $rsPadrao,$boTransacao );
            $this->obTPessoalCargoPadrao->setDado ( "cod_cargo" , $this->getCodCargo() );
            $this->obTPessoalCargoPadrao->setDado ( "cod_padrao", $this->obRFolhaPagamentoPadrao->getCodPadrao() );
            $obErro = $this->obTPessoalCargoPadrao->inclusao ( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->roUltimoCargoSubDivisao->incluirVagas ( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                $this->obTPessoalCboCargo->setDado    ( "cod_cbo", $this->getCBO());
                $this->obTPessoalCboCargo->setDado    ( "cod_cargo", $this->getCodCargo());
                $obErro = $this->obTPessoalCboCargo->inclusao ( $boTransacao );
            }
        // Se TEM especialidade
        } else {
            for ($inCount=0; $inCount<count($this->arRPessoalEspecialidade); $inCount++) {
                $obErro = $this->arRPessoalEspecialidade[$inCount]->incluirEspecialidade ( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }

    if ( !$obErro->ocorreu() ) {
        $stComplementoChaveCargoRequisito = $this->obTPessoalCargoRequisito->getComplementoChave();
        $this->obTPessoalCargoRequisito->setComplementoChave( 'cod_cargo' );
        $this->obTPessoalCargoRequisito->setDado( 'cod_cargo', $this->inCodCargo );

        $obErro = $this->obTPessoalCargoRequisito->exclusao( $boTransacao );

        $this->obTPessoalCargoRequisito->setComplementoChave( $stComplementoChaveCargoRequisito );
    }

    if ( !$obErro->ocorreu() && count($this->arCodRequisitos) ) {
        $this->obTPessoalCargoRequisito->setDado( 'cod_cargo', $this->inCodCargo );
        foreach ($this->arCodRequisitos as $inCodRequisito) {
            $this->obTPessoalCargoRequisito->setDado( 'cod_requisito', $inCodRequisito );
            $obErro = $this->obTPessoalCargoRequisito->inclusao( $boTransacao );

            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }

    if ( !$obErro->ocorreu() ) {
        //O Restante dos valores vem setado da pagina de processamento
        $arChaveAtributoCandidato =  array( "cod_cargo" => $this->getCodCargo() );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
        $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCargo );

    return $obErro;
}

/**
    * Vincula todos os eventos já cadastrados ao novo cargo criado
    * @access Public
    * @return Object Objeto Erro
*/
function incluirVincularCargoEventos()
{
    $obTFolhaPagamentoConfiguracaoEventoCasoCargo = new TFolhaPagamentoConfiguracaoEventoCasoCargo();
    $obErro = new Erro;
    $obTransacao =  new Transacao;
    $boTransacao = "";

    $this->obTFolhaPagamentoConfiguracaoEventoCaso->recuperaRelacionamentoCargoEventos($rsResutadoConsulta, $this->obTPessoalCargo->getDado('cod_cargo'));

    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    foreach ($rsResutadoConsulta->getElementos() as $valor) {

       $obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado('cod_caso', $valor['cod_caso']);
       $obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado('cod_evento', $valor['cod_evento']);
       $obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado('timestamp', $valor['timestamp']);
       $obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado('cod_configuracao', $valor['cod_configuracao']);
       $obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado('cod_cargo', $valor['cod_cargo']);

       if ( !$obErro->ocorreu() ) {
           $obErro = $obTFolhaPagamentoConfiguracaoEventoCasoCargo->inclusao($boTransacao);

       }
    }

    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoConfiguracaoEventoCasoCargo );

    return $obErro;
}

/**
    * Busca Padrao do cargo
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultaCargoPadrao(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodCargo) {
       $stFiltro .= "and PC.cod_cargo =".$this->inCodCargo."  \n";
    }
    $obErro = $this->obTPessoalCargoPadrao->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;

}

/**
    * Lista todos cargos de acordo com variaveis setadas
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarCargo(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodCargo) {
       $stFiltro .= " cod_cargo = ".$this->inCodCargo." AND ";
    }
    if ($this->stDescricao) {
        $stFiltro .= " UPPER(descricao) LIKE UPPER('%".$this->stDescricao."%') AND ";
    }

    $stOrdem = " ORDER BY descricao";
    $stFiltro = ($stFiltro) ?" WHERE ".substr($stFiltro,0,strlen($stFiltro)-4) : "";
    $obErro = $this->obTPessoalCargo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;

}

/**
    * Lista todos cargos de uma lista de códigos
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @param  Array  $arCodCargos Parâmetro array de códigos de cargos
    * @return Object Objeto Erro
*/
function listarCargosEspecialidadePorCodigo(&$rsRecordSet , $arCargosEspecialidade, $boTransacao = "")
{
    if ( is_array($arCargosEspecialidade) ) {
        foreach ($arCargosEspecialidade as $stCargoEspecialidade) {
            $stFiltro .= "'".$stCargoEspecialidade . "',";
        }
        $stFiltro = substr($stFiltro,0,strlen($stFiltro)-1);
        $stFiltro = " WHERE cargo_esp IN (".$stFiltro.")";
        $stOrdem = " ORDER BY descricao";
        $obErro = $this->obTPessoalCargo->recuperaCargosEspecialidadePorCodigo( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    } else {
        $obErro = new erro;
        $rsRecordSet = new RecordSet;
    }

    return $obErro;

}

/**
    * Adiciona um CargoSubDivisao ao array de referencia-objeto
    * @access Public
*/
function addCargoSubDivisao()
{
    $this->arRPessoalCargoSubDivisao[] = new RPessoalCargoSubDivisao ( $this );
    $this->roUltimoCargoSubDivisao     = &$this->arRPessoalCargoSubDivisao[ count($this->arRPessoalCargoSubDivisao) - 1 ];
}

/**
    * Retira um CargoSubDivisao do array de referencia-objeto
    * @access Public
*/
function commitCargoSubDivisao()
{
    $this->arRPessoalCargoSubDivisao = array_pop ($this->arRPessoalCargoSubDivisao);
}

/**
    * Lista todos cargo_sub_divisao de acordo com variaveis setadas
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarVagas(&$rsRecordSet, $stFiltro = "", $stOrder = "" , $boTransacao = "")
{
    if ($this->roPessoalCargo->inCodCargo) {
        $stFiltro .= " AND cod_cargo = ".$this->roPessoalCargo->inCodCargo ." ";
    }

    $stOrder = ($stOrder) ? " ORDER BY ".$stOrder : "";
    $obErro = $this->obTPessoalSubDivisao->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;

}

/**
    * Lista todos cargo_sub_divisao de acordo com variaveis setadas
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarCargosPorSubDivisao(&$rsRecordSet , $boTransacao = "")
{
    if ( $this->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao() ) {
        $stFiltro .= " WHERE cod_sub_divisao = ".$this->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao();
    }
    $stOrder = ' ORDER BY descricao ';
    $obErro  = $this->obTPessoalCargo->recuperaCargosPorSubDivisao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;

}

/**
    * Lista todos cargo_sub_divisao que estão com norma em vigor
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarCargosPorSubDivisaoServidor(&$rsRecordSet , $boTransacao = "")
{
    include_once CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoPeriodoMovimentacao.class.php';
    $obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;

    if ($this->getExercicio()) {
        $stFiltro .= " AND EXTRACT(YEAR FROM dt_inicial) = '".$this->getExercicio()."' ";
    }

    if ($this->getCodMes()) {
        $stFiltro .= " AND EXTRACT(MONTH FROM dt_inicial) = ".$this->getCodMes()." ";
    }

    $obRFolhaPagamentoPeriodoMovimentacao->recuperaAnosPeriodoMovimentacao($rsUltimoPeriodoMovimentacao, $stFiltro, $boTransacao);

    $stFiltro = " AND FPM.cod_periodo_movimentacao = ".$rsUltimoPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
    $obRFolhaPagamentoPeriodoMovimentacao->listarPeriodoMovimentacao($rsPeriodoMovimentacao, $stFiltro, "", $boTransacao);

    $stFiltro  = " AND ( dt_publicacao <= to_date('".$rsPeriodoMovimentacao->getCampo('dt_final')."', 'dd/mm/yyyy')   \n";
    $stFiltro .= "   AND ( dt_termino IS NULL                                                                         \n";
    $stFiltro .= "      OR dt_termino >= to_date('".$rsPeriodoMovimentacao->getCampo('dt_inicial')."', 'dd/mm/yyyy')) \n";

    if ($this->getCodCargo()) {
        if ($this->getBuscarCargosNormasVencidas() === true) {
            $stFiltro .= "  OR cod_cargo = ".$this->getCodCargo();
        } else {
            $stFiltro .= " AND cod_cargo = ".$this->getCodCargo();
        }
    }

    if ($this->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao() ) {
        $stFiltro .= " ) AND cod_sub_divisao IN (".$this->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao().") ";
    } else {
        $stFiltro .= " ) ";
    }

    $stOrder = ' GROUP BY cod_cargo, descricao, cargo_cc, funcao_gratificada, cod_escolaridade, atribuicoes, dt_termino ORDER BY descricao ';
    $obErro  = $this->obTPessoalCargo->recuperaCargosPorSubDivisaoServidor( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;

}

/**
    * Lista todos cargo_sub_divisao de acordo com variaveis setadas
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  String $stCodigos codigos de sub_divisao para filtro no formato: 12,45,89,52
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarCargoDeCodigosSubDivisao(&$rsRecordSet, $stCodigos, $boTransacao = "")
{
    $stFiltro .= " WHERE cod_sub_divisao IN (".$stCodigos.")";
    $stOrder = ' ORDER BY descricao ';
    $obErro  = $this->obTPessoalCargo->recuperaCargosPorSubDivisao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;

}

/**
    * Altera os dados do cargo
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarCargo($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalCargo->setDado ( "cod_cargo"         , $this->getCodCargo()        );
        $this->obTPessoalCargo->setDado ( "descricao"         , $this->getDescricao()       );
        $this->obTPessoalCargo->setDado ( "cargo_cc"          , $this->getCargo()           );
        $this->obTPessoalCargo->setDado ( "funcao_gratificada", $this->getFuncao()          );
        $this->obTPessoalCargo->setDado ( "cod_escolaridade"  , $this->getCodEscolaridade() );
        $this->obTPessoalCargo->setDado ( "atribuicoes"       , $this->getAtribuicoes()     );
        $obErro = $this->obTPessoalCargo->alteracao ( $boTransacao );
    }

    // Início das alterações do padrão dos servidores e atualizando o salário
    if ( !$obErro->ocorreu() ) {
        // Busca o ultimo periodo de movimentação
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsCompetencia,""," ORDER BY dt_final DESC LIMIT 1",$boTransacao);

        // Recupera o codigo do padrão antigo, para poder achar os servidor e alterar para o padrão novo
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCargoPadrao.class.php");
        $obTPessoalCargoPadrao = new TPessoalCargoPadrao();
        $stFiltro = " AND pc.cod_cargo = ".$this->getCodCargo();
        $obTPessoalCargoPadrao->recuperaRelacionamento($rsCargoPadrao,$stFiltro,"",$boTransacao);

        if ($rsCargoPadrao->getNumLinhas() != -1) {
            // Caso o padrão tenha realmente sido alterado, atualiza o padrão no contrato dos servidores
            if ($rsCargoPadrao->getCampo("cod_padrao") != $this->obRFolhaPagamentoPadrao->getCodPadrao()) {

                // busca os servidores no padrão do cargo antigo
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorPadrao.class.php");
                $obTPessoalContratoServidorPadrao = new TPessoalContratoServidorPadrao();
                $obTPessoalContratoServidorPadrao->setDado("inCodPeriodoMovimentacao", $rsCompetencia->getCampo("cod_periodo_movimentacao"));
                $obTPessoalContratoServidorPadrao->setDado("stSituacoContrato", "'A'");
                $stFiltro  = " AND ultimo_contrato_servidor_padrao.cod_padrao = ".$rsCargoPadrao->getCampo("cod_padrao");
                $stFiltro .= " AND ultimo_contrato_servidor_funcao.cod_cargo = ".$this->getCodCargo();
                $obTPessoalContratoServidorPadrao->recuperaPadraoServidor($rsServidorContratoPadrao,$stFiltro,"",$boTransacao);

                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPadrao.class.php");
                $stFiltro = " WHERE cod_padrao = ".$this->obRFolhaPagamentoPadrao->getCodPadrao();
                $obTFolhaPagamentoPadrao = new TFolhaPagamentoPadrao();
                $obTFolhaPagamentoPadrao->recuperaTodos($rsPadraoNovo,$stFiltro,"",$boTransacao);

                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSalario.class.php");
                $obTPessoalContratoServidorSalario = new TPessoalContratoServidorSalario();

                while (!$rsServidorContratoPadrao->eof()) {
                    if (!$obErro->ocorreu()) {
                        // Atualiza padrão dos servidores
                        $obTPessoalContratoServidorPadrao->setDado("cod_contrato", $rsServidorContratoPadrao->getCampo("cod_contrato"));
                        $obTPessoalContratoServidorPadrao->setDado("cod_padrao"  , $this->obRFolhaPagamentoPadrao->getCodPadrao());
                        $obErro = $obTPessoalContratoServidorPadrao->inclusao($boTransacao);
                    }

                    if (!$obErro->ocorreu()) {
                        // Verifica se salário foi alterado manualmente, CASO SIM, não atualiza o salário do contrato
                        $stFiltro  = " AND salario.cod_contrato = ".$rsServidorContratoPadrao->getCampo("cod_contrato");
                        $obTPessoalContratoServidorSalario->recuperaRelacionamento($rsSalario, $stFiltro, "", $boTransacao);

                        // Atualiza salário servidores
                        if ($rsSalario->getCampo("salario") == $rsCargoPadrao->getCampo("valor")) {
                            $obTPessoalContratoServidorSalario->setDado("cod_contrato"             , $rsServidorContratoPadrao->getCampo("cod_contrato"));
                            $obTPessoalContratoServidorSalario->setDado("cod_periodo_movimentacao" , $rsCompetencia->getCampo("cod_periodo_movimentacao"));
                            $obTPessoalContratoServidorSalario->setDado("vigencia"                 , $rsCargoPadrao->getCampo("vigencia"));
                            $obTPessoalContratoServidorSalario->setDado("salario"                  , $this->calculaSalarioPadrao($boTransacao, $rsServidorContratoPadrao->getCampo("cod_contrato")));
                            $obTPessoalContratoServidorSalario->setDado("horas_mensais"            , $rsPadraoNovo->getCampo("horas_mensais"));
                            $obTPessoalContratoServidorSalario->setDado("horas_semanais"           , $rsPadraoNovo->getCampo("horas_semanais"));
                            $obErro = $obTPessoalContratoServidorSalario->inclusao($boTransacao);
                        }
                    }
                    $rsServidorContratoPadrao->proximo();
                }
            }
        }
    }
    // Fim das alterações do padrão dos servidores e atualizando o salário

    if ( !$obErro->ocorreu() ) {
        // Se NÃO tem especialidade
        if (!$this->getEspecialidade()) {
            $this->obRFolhaPagamentoPadrao->listarPadrao( $rsPadrao,$boTransacao );
            $this->obTPessoalCargoPadrao->setDado ( "cod_cargo" , $this->getCodCargo() );
            $this->obTPessoalCargoPadrao->setDado ( "cod_padrao", $this->obRFolhaPagamentoPadrao->getCodPadrao () );
            $obErro = $this->obTPessoalCargoPadrao->inclusao ( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->roUltimoCargoSubDivisao->incluirVagas ( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                $this->obTPessoalCboCargo->setDado    ( "cod_cbo", $this->getCBO());
                $this->obTPessoalCboCargo->setDado    ( "cod_cargo", $this->getCodCargo());
                $obErro = $this->obTPessoalCboCargo->inclusao ( $boTransacao );
            }
        // Se TEM especialidade
        } else {
            $this->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsRecordSet , $boTransacao );
            $arEspe = array();
            $rsRecordSet->preenche($arEspe);
            for ($inCount=0; $inCount<count($this->arRPessoalEspecialidade); $inCount++) {
                if ($arEspe[$inCount][cod_especialidade] == $this->arRPessoalEspecialidade[$inCount]->getCodEspecialidade() ) {
                    $obErro = $this->arRPessoalEspecialidade[$inCount]->incluirEspecialidade ( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                } else {
                    $obErro = $this->arRPessoalEspecialidade[$inCount]->alterarEspecialidade ( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }
    }

    if ( !$obErro->ocorreu() ) {
        $stComplementoChaveCargoRequisito = $this->obTPessoalCargoRequisito->getComplementoChave();
        $this->obTPessoalCargoRequisito->setComplementoChave( 'cod_cargo' );
        $this->obTPessoalCargoRequisito->setDado( 'cod_cargo', $this->inCodCargo );

        $obErro = $this->obTPessoalCargoRequisito->exclusao( $boTransacao );

        $this->obTPessoalCargoRequisito->setComplementoChave( $stComplementoChaveCargoRequisito );
    }

    if ( !$obErro->ocorreu() && count($this->arCodRequisitos) ) {
        $this->obTPessoalCargoRequisito->setDado( 'cod_cargo', $this->inCodCargo );
        foreach ($this->arCodRequisitos as $inCodRequisito) {
            $this->obTPessoalCargoRequisito->setDado( 'cod_requisito', $inCodRequisito );
            $obErro = $this->obTPessoalCargoRequisito->inclusao( $boTransacao );

            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }

    if ( !$obErro->ocorreu() ) {
        //O Restante dos valores vem setado da pagina de processamento
        $arChaveAtributoCandidato =  array( "cod_cargo" => $this->getCodCargo() );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
        $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCargo );

    return $obErro;
}

/**
    * Excluir todos os eventos vinculados  ao cargo,
*/
function excluirVincularCargoEventos($boTransacao)
{
    $obTFolhaPagamentoConfiguracaoEventoCasoCargo = new TFolhaPagamentoConfiguracaoEventoCasoCargo();
    $obErro = new Erro;
    $rsResutadoConsulta= new RecordSet;
    $stFiltro = " where cod_cargo = ".$this->obTPessoalCargo->getDado('cod_cargo');

    $obTransacao =  new Transacao;
    $boFlagTransacao = false;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu () ) {
        $obTFolhaPagamentoConfiguracaoEventoCasoCargo->recuperaTodos($rsResutadoConsulta, $stFiltro, "",$obTransacao);

        if ($rsResutadoConsulta->getNumLinhas != -1) {
            foreach ($rsResutadoConsulta->getElementos() as $valor) {
                $obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado('cod_caso', $valor['cod_caso']);
                $obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado('cod_evento', $valor['cod_evento']);
                $obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado('timestamp', $valor['timestamp']);
                $obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado('cod_configuracao', $valor['cod_configuracao']);
                $obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado('cod_cargo', $valor['cod_cargo']);

                if ( !$obErro->ocorreu() ) {
                    $obErro = $obTFolhaPagamentoConfiguracaoEventoCasoCargo->exclusao($boTransacao);
                }
            }
        }

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoConfiguracaoEventoCasoCargo );
    }

    return $obErro;
}

/**
    * Excluir os dados do cargo, cargo_padrao, cargo_subdiv
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirCargo($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu () ) {
        $this->obTPessoalCargo->setDado ( "cod_cargo" , $this->inCodCargo );
        $obErro = $this->excluirVincularCargoEventos($boTransacao);
        $obErro = $this->obTPessoalCargo->validaExclusao("", $boTransacao);
    }
    if ( !$obErro->ocorreu () ) {
        $this->addEspecialidade();
        $this->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidades , $boTransacao );
        if ( !$obErro->ocorreu () ) {
            //Se tem especialidade
            if ( $rsEspecialidades->getCampo('cod_especialidade') ) {
                $this->setEspecialidade(true);
                $inCodEspecialidadeAtual = 0;
                while ( !$rsEspecialidades->eof() ) {
                    if ($inCodEspecialidadeAtual != $rsEspecialidades->getCampo('cod_especialidade') ) {
                        $inCodEspecialidadeAtual = $rsEspecialidades->getCampo('cod_especialidade');
                        $this->roUltimoEspecialidade->setCodEspecialidade( $rsEspecialidades->getCampo('cod_especialidade') );
                        $obErro = $this->roUltimoEspecialidade->excluirEspecialidade($boTransacao);
                    }
                    $rsEspecialidades->proximo();
                }
            } else {
                if ( !$obErro->ocorreu() ) {
                    $this->obTPessoalCboCargo->setDado    ( "cod_cargo", $this->getCodCargo());
                    $obErro = $this->obTPessoalCboCargo->exclusao ( $boTransacao );
                }
                $this->setEspecialidade(false);
                $this->obTPessoalCargoPadrao->setDado ( "cod_cargo" , $this->inCodCargo );
                $obErro = $this->obTPessoalCargoPadrao->exclusao($boTransacao);
                if ( !$obErro->ocorreu() ) {
                    $this->addCargoSubDivisao();
                    $obErro = $this->roUltimoCargoSubDivisao->excluirVagas ($boTransacao);
                }
            }

            if ( !$obErro->ocorreu() ) {
                $obTPessoalLoteFeriasFuncao = new TPessoalLoteFeriasFuncao();
                $obTPessoalLoteFeriasFuncao->setDado('cod_cargo', $this->obTPessoalCargo->getDado('cod_cargo'));
                $obErro = $obTPessoalLoteFeriasFuncao->exclusao($boTransacao);
            }

            /*********************/
            if ( !$obErro->ocorreu()) {
                $obTPessoalEspecialidade = new TPessoalEspecialidade();
                $obTPessoalEspecialidade->setDado('cod_cargo', $this->obTPessoalCargo->getDado('cod_cargo'));
                $obTPessoalEspecialidade->recuperaPorCargo($rsPessoalEspecialidade);

                while (!$rsPessoalEspecialidade->eof()) {
                    if ( !$obErro->ocorreu()) {
                        $obTPessoalEspecialidadePadrao = new TPessoalEspecialidadePadrao();
                        $obTPessoalEspecialidadePadrao->setDado('cod_especialidade', $rsPessoalEspecialidade->getCampo('cod_especialidade'));
                        $obErro = $obTPessoalEspecialidadePadrao->exclusao($boTransacao);
                    }

                    if ( !$obErro->ocorreu()) {
                        $obTPessoalCboEspecialidade = new TPessoalCboEspecialidade();
                        $obTPessoalCboEspecialidade->setDado('cod_especialidade', $rsPessoalEspecialidade->getCampo('cod_especialidade'));
                        $obErro = $obTPessoalCboEspecialidade->exclusao($boTransacao);
                    }

                    if ( !$obErro->ocorreu()) {
                        $obTPessoalEspecialidade = new TPessoalEspecialidade();
                        $obTPessoalEspecialidade->setDado('cod_especialidade', $rsPessoalEspecialidade->getCampo('cod_especialidade'));
                        $obErro = $obTPessoalEspecialidade->exclusao($boTransacao);
                    }

                    $rsPessoalEspecialidade->proximo();
                }
            }

            if ( !$obErro->ocorreu()) {
                $obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade = new TFolhaPagamentoConfiguracaoEventoCasoEspecialidade();
                $obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade->setDado('cod_cargo', $this->obTPessoalCargo->getDado('cod_cargo'));
                $obErro = $obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade->exclusao($boTransacao);
            }

            if ( !$obErro->ocorreu()) {
                $obTFolhaPagamentoConfiguracaoEventoCasoCargo = new TFolhaPagamentoConfiguracaoEventoCasoCargo();
                $obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado('cod_cargo', $this->obTPessoalCargo->getDado('cod_cargo'));
                $obErro = $obTFolhaPagamentoConfiguracaoEventoCasoCargo->exclusao($boTransacao);
            }
            /*********************/

            if ( !$obErro->ocorreu() ) {
                $stComplementoChaveCargoRequisito = $this->obTPessoalCargoRequisito->getComplementoChave();
                $this->obTPessoalCargoRequisito->setComplementoChave('cod_cargo');
                $this->obTPessoalCargoRequisito->setDado('cod_cargo', $this->obTPessoalCargo->getDado('cod_cargo'));

                $obErro = $this->obTPessoalCargoRequisito->exclusao( $boTransacao );

                $this->obTPessoalCargoRequisito->setComplementoChave($stComplementoChaveCargoRequisito);
            }

            if ( !$obErro->ocorreu() ) {
                $obErro = $this->obTPessoalCargo->exclusao ($boTransacao);
            }
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCargo );
        }
    }
    if ( !$obErro->ocorreu() ) {
        //O Restante dos valores vem setado da pagina de processamento
        $arChaveAtributoCandidato =  array( "cod_cargo" => $this->getCodCargo() );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
        $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
    }

    return $obErro;
}

/**
    * Consulta a existência de um servidor com cargo cadastrado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarCargoServidor($boTransacao = "")
{
    $this->obTPessoalCargo->setDado( "cod_cargo" , $this->getCodCargo() );
    $obErro = $this->obTPessoalCargo->RecuperaCargoServidor($rsCargoServidor,$boTransacao = "");
    if ( !$obErro->ocorreu()) {
        $this->setCodCargo($rsCargoServidor->getCampo("cod_cargo"));
    }

   return $obErro;
}

/**
    * Adiciona um CargoSubDivisao ao array de referencia-objeto
    * @access Public
*/
function addEspecialidade()
{
    $this->arRPessoalEspecialidade[] = new RPessoalEspecialidade ( $this );
    $this->roUltimoEspecialidade     = &$this->arRPessoalEspecialidade[ count($this->arRPessoalEspecialidade) - 1 ];
}

/**
    * Retira um CargoSubDivisao do array de referencia-objeto
    * @access Public
*/
function commitEspecialidade()
{
    $this->arRPessoalEspecialidade = array_pop ($this->arRPessoalEspecialidade);
}

function calculaSalarioPadrao(&$boTransacao, $inCodContrato)
{
    $nuValor         = "0.00";
    $inCodPadrao     = $this->obRFolhaPagamentoPadrao->getCodPadrao();
    $inCodProgressao = $this->getProgressao($boTransacao, $inCodContrato);

    // Verifica se existe progressão para o padrão do cargo
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoNivelPadraoNivel.class.php");
    $stFiltro = " AND FPNP.cod_padrao = ".$this->obRFolhaPagamentoPadrao->getCodPadrao();
    $obTFolhaPagamentoNivelPadraoNivel = new TFolhaPagamentoNivelPadraoNivel();
    $obTFolhaPagamentoNivelPadraoNivel->recuperaRelacionamento($rsNivelPadraoNivel,$stFiltro,"",$boTransacao);

    // CASO SIM, padrão do cargo POSSUI progressão
    if ($rsNivelPadraoNivel->getNumLinhas() != -1 && trim($inCodProgressao)!="") {
        $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
        $obRFolhaPagamentoPadrao->setCodPadrao( $inCodPadrao );
        $obRFolhaPagamentoPadrao->addNivelPadrao();
        $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setCodNivelPadrao( $inCodProgressao);
        $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->listarNivelPadrao( $rsProgressao,$boTransacao );
        $nuValor = $rsProgressao->getCampo("valor");
    } else {
        // CASO NÃO, padrão do cargo NÃO POSSUI progressão
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPadraoPadrao.class.php");
        $stFiltro = " AND padrao_padrao.cod_padrao = ".$this->obRFolhaPagamentoPadrao->getCodPadrao();
        $obTFolhaPagamentoPadraoPadrao = new TFolhaPagamentoPadraoPadrao();
        $obTFolhaPagamentoPadraoPadrao->recuperaRelacionamento($rsPadraoPadrao,$stFiltro,"",$boTransacao);
        $nuValor = $rsPadraoPadrao->getCampo("valor");
    }

    return $nuValor;
}

function getProgressao(&$boTransacao, $inCodContrato)
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorInicioProgressao.class.php");

    $obTPessoalContratoServidorInicioProgressao = new TPessoalContratoServidorInicioProgressao();
    $stFiltro = " AND inicio_progressao.cod_contrato = ".$inCodContrato;
    $obTPessoalContratoServidorInicioProgressao->recuperaRelacionamento($rsContratoServidorInicioProgressao,$stFiltro,"",$boTransacao);
    $stDataProgressao = $rsContratoServidorInicioProgressao->getCampo("dt_inicio_progressao");

    $inCodPadrao      = $this->obRFolhaPagamentoPadrao->getCodPadrao();
    $inCodProgressao  = "";

    if ( trim($inCodPadrao) != "" and trim($stDataProgressao) != "" ) {
        //calcula diferença de meses entre datas
        $stDataProgressao    = explode('/',$stDataProgressao);
        $inMesProgressao = $stDataProgressao[1];
        $inAnoProgressao = $stDataProgressao[2];

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao,"","",$boTransacao);

        $dtDataAtual = explode('/',$rsPeriodoMovimentacao->getCampo("dt_final"));
        $inMesAtual  = $dtDataAtual[1];
        $inAnoAtual  = $dtDataAtual[2];

        $inMeses = 0;
        if ($inAnoAtual >= $inAnoProgressao) {
            $inMeses = ($inMesAtual - $inMesProgressao) + (($inAnoAtual - $inAnoProgressao) * 12);
        } elseif ($inMesAtual >= $inMesProgressao) {
            $inMeses = $inMesAtual - $inMesProgressao;
        }

        //Lista as progressões, a última progressão do rsProgressao é a progressão do padrão para esta data de início de progressão
        $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
        $obRFolhaPagamentoPadrao->setCodPadrao( $inCodPadrao );
        $obRFolhaPagamentoPadrao->addNivelPadrao();
        $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setQtdMeses( $inMeses );
        $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->listarNivelPadrao( $rsProgressao, $boTransacao );
        $rsProgressao->setUltimoElemento();

        if ( $rsProgressao->getNumLinhas() > 0 ) {
            $inCodProgressao = $rsProgressao->getCampo('cod_nivel_padrao');
        }
    }

    return $inCodProgressao;
}

}
?>
