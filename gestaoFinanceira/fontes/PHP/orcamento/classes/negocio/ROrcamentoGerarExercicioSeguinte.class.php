<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos (urbem@cnm.org.br)      *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo  sob *
    * os termos da Licença Pública Geral GNU conforme publicada pela  Free  Software *
    * Foundation; tanto a versão 2 da Licença, como (a seu critério) qualquer versão *
    * posterior.                                                                     *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral  do  GNU  junto  com *
    * este programa; se não, escreva para  a  Free  Software  Foundation,  Inc.,  no *
    * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.               *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Classe de Regra de Negócio para Gerar Exercicio Seguinte
    * Data de Criação: 25/07/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.31
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS."Transacao.class.php";

class ROrcamentoGerarExercicioSeguinte
{
    /**
        * @var Object
        * @access Private
    */
    var $obTransacao;
    /**
        * @var String
        * @access Private
    */
    var $stExercicio;
    /**
        * @var String
        * @access Private
    */
    var $stOpcoesSelecionadas;
    
    /**
         * @access Public
         * @param String $valor
    */
    public function setExercicio($valor) { $this->stExercicio  = $valor;                          }
    /**
         * @access Public
         * @param String $valor
    */
    public function setOpcoesSelecionadas($valor) { $this->stOpcoesSelecionadas = $valor;                  }
    
    /**
         * @access Public
         * @param String $valor
    */
    public function getExercicio() { return $this->stExercicio;                           }
    /**
         * @access Public
         * @param String $valor
    */
    public function getOpcoesSelecionadas() { return $this->stOpcoesSelecionadas;                  }
    
    /**
        * Método Construtor
        * @access Private
    */
    public function ROrcamentoGerarExercicioSeguinte()
    {
        $this->obTransacao                = new Transacao;
    }
    
    /**
        * Inclui dados no banco
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    
    public function verificaPPA(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaVerificaPPA();
        //$this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaVerificaPPA()
    {
        $stSql = "
          SELECT ppa.cod_ppa
            FROM ppa.ppa
           WHERE ppa.ano_inicio <= BTRIM(TO_CHAR(TO_NUMBER('".$this->stExercicio."','9999') + 1, '9999'))
             AND ppa.ano_final  >= BTRIM(TO_CHAR(TO_NUMBER('".$this->stExercicio."','9999') + 1, '9999'))
        ";
        return $stSql;
    }
    
    public function incluir(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        include_once CAM_GF_ORC_MAPEAMENTO.'FOrcamentoGerarOrcamento.class.php';
        $obFOrcamentoGerarOrcamento = new FOrcamentoGerarOrcamento;
    
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        if (!$obErro->ocorreu()) {
            $obFOrcamentoGerarOrcamento->setDado('stExercicio' , $this->stExercicio);
            $obFOrcamentoGerarOrcamento->setDado('stParametros', $this->stOpcoesSelecionadas);        
            $obFOrcamentoGerarOrcamento->setDado('stFiltro'    , $stFiltro);
            $obErro = $obFOrcamentoGerarOrcamento->recuperaTodos($rsRecordSet, '', '', $boTransacao);
        }
        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTOrcamentoReserva);
    
        return $obErro;
    }
}