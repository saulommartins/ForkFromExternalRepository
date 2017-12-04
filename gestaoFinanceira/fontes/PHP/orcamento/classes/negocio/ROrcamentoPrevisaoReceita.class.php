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
    * Classe de Regra de Negócio Previsão Receita
    * Data de Criação   : 14/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @package URBEM
    * @subpackage Regra

    $Id: ROrcamentoPrevisaoReceita.class.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 30824 $
    $Name$
    $Author: melo $
    $Date: 2008-02-13 15:31:44 -0200 (Qua, 13 Fev 2008) $

    * Casos de uso: uc-02.01.06
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"     );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"    );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoOrcamentaria.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php");
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"              );

/**
* Classe de Regra de Negócio Previsão Receita
* Data de Criação   : 14/07/2004
* @author Analista: Jorge B. Ribarr
* @author Desenvolvedor: Roberto Pawelski Rodrigues
*/
class ROrcamentoPrevisaoReceita
{
    /**
    * @var Integer
    * @access Private
    */
    public $inPeriodo;

    /**
    * @var Array
    * @access Private
    */
    public $arID;

    /**
    * @var Integer
    * @access Private
    */
    public $inQtdColunas;

    /**
    * @var Integer
    * @access Private
    */
    public $inQtdLinhas;

    /**
    * @var Integer
    * @access Private
    */
    public $inCodigoReceita;

    /**
    * @var Float
    * @access Private
    */
    public $flValorPrevisto;

    /**
    * @var Integer
    * @access Private
    */
    public $inExercicio;

    /**
    * @var Objeto
    * @access Private
    */
    public $obROrcamentoReceita;

    /**
    * @var Objeto
    * @access Private
    */
    public $obROrcamentoPrevisaoOrcamentaria;

    /**
    * @var Objeto
    * @access Private
    */
    public $obRConfiguracaoOrcamento;

    /**
    * @var Objeto
    * @access Private
    */
    public $obROrcamentoEntidade;

    /**
    * @var Objeto
    * @access Private
    */
    public $obTransacao;

    /**
    * @access Private
    * @var String
    */
    public $stCodEstruturalInicial;

    /**
    * @access Private
    * @var String
    */
    public $stCodEstruturalFinal;

    /**
    * @access Private
    * @var String
    */
    public $inCodReceitaInicial;

    /**
    * @access Private
    * @var String
    */
    public $inCodReceitaFinal;

    /**
    * @access Private
    * @var String
    */
    public $stCodEstruturalDedutora;

    /**
    * @access Public
    * @param Object $valor
    */
    public function setROrcamentoReceita($valor) { $this->obROrcamentoReceita       = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setTransacao($valor) { $this->obTransacao       = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setCodigoReceita($valor) { $this->inCodigoReceita  = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setID($valor) { $this->arID             = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setQtdColunas($valor) { $this->inQtdColunas     = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setQtdLinhas($valor) { $this->inQtdLinhas      = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setPeriodo($valor) { $this->inPeriodo        = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setValorPeriodo($valor) { $this->flValorPrevisto  = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setExercicio($valor) { $this->inExercicio      = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setROrcamentoPrevisaoOrcamentaria($valor) { $this->obROrcamentoPrevisaoOrcamentaria = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setROrcamentaria($valor) { $this->obROrcamentaria = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setRConfiguracaoOrcamento($valor) { $this->obRConfiguracaoOrcamento = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setROrcamentoEntidade($valor) { $this->obROrcamentoEntidade = $valor; }

    /**
     * @access Public
     * @param Integer $valor
    */
    public function setCodEstruturalInicial($valor) { $this->stCodEstruturalInicial       = $valor; }

    /**
         * @access Public
         * @param Integer $valor
    */
    public function setCodEstruturalFinal($valor) { $this->stCodEstruturalFinal           = $valor; }
    /**
     * @access Public
     * @param Integer $valor
    */
    public function setCodReceitaInicial($valor) { $this->inCodReceitaInicial       = $valor; }

    /**
         * @access Public
         * @param Integer $valor
    */
    public function setCodReceitaFinal($valor) { $this->inCodReceitaFinal           = $valor; }

    /**
         * @access Public
         * @param String $valor
    */
    public function setCodEstruturalDedutora($valor) { $this->stCodEstruturalDedutora  = $valor; }

    /**
    * @access Public
    * @return Object
    */
    public function getROrcamentoReceita() { return $this->obROrcamentoReceita;      }

    /**
    * @access Public
    * @return Object
    */
    public function getTransacao() { return $this->obTransacao;      }

    /**
    * @access Public
    * @return Integer
    */
    public function getCodigoReceita() { return $this->inCodigoReceita; }

    /**
    * @access Public
    * @return Integer
    */
    public function getID() { return $this->arID;             }

    /**
    * @access Public
    * @return Integer
    */
    public function getQtdColunas() { return $this->inQtdColunas;     }

    /**
    * @access Public
    * @return Integer
    */
    public function getQtdLinhas() { return $this->inQtdLinhas;      }

    /**
    * @access Public
    * @return Integer
    */
    public function getPeriodo() { return $this->inPeriodo;       }

    /**
    * @access Public
    * @return Integer
    */
    public function getExercicio() { return $this->inExercicio;     }

    /**
    * @access Public
    * @return Float
    */
    public function getValorPeriodo() { return $this->flValorPrevisto; }

    /**
    * @access Public
    * @return Object
    */
    public function getROrcamentoPrevisaoOrcamentaria() { return $this->obROrcamentoPrevisaoOrcamentaria; }

    /**
    * @access Public
    * @return Object
    */
    public function getRConfiguracaoOrcamento() { return $this->obRConfiguracaoOrcamento; }

    /**
    * @access Public
    * @return Object
    */
    public function getROrcamentoEntidade() { return $this->obROrcamentoEntidade;   }

    /**
    * @access Public
    * @return Object
    */
    public function getCodEstruturalInicial() { return $this->stCodEstruturalInicial;   }

    /**
    * @access Public
    * @return Object
    */
    public function getCodEstruturalFinal() { return $this->stCodEstruturalFinal;   }

    /**
    * @access Public
    * @return Object
    */
    public function getCodReceitaInicial() { return $this->inCodReceitaInicial;   }

    /**
    * @access Public
    * @return Object
    */
    public function getCodReceitaFinal() { return $this->inCodReceitaFinal;   }

    /**
    * @access Public
    * @return Object
    */
    public function getCodEstruturalDedutora() { return $this->stCodEstruturalDedutora; }

    /**
    * Método Construtor
    * @access Private
    */
    public function ROrcamentoPrevisaoReceita()
    {
        $this->setTransacao                         ( new Transacao                     );
        $this->setROrcamentoPrevisaoOrcamentaria    ( new ROrcamentoPrevisaoOrcamentaria);
        $this->setROrcamentoEntidade                ( new ROrcamentoEntidade            );
        $this->setRConfiguracaoOrcamento            ( new ROrcamentoConfiguracao        );
        $this->setROrcamentoReceita                 ( new ROrcamentoReceita             );
        $this->setExercicio                         ( Sessao::getExercicio()                );
    }

    /**
    * Cadastra e/ou Altera Previsão Receita
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    */
    public function salvar($boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrevisaoReceita.class.php" );
        $obTOrcamentoPrevisaoReceita          = new TOrcamentoPrevisaoReceita;
            $obErro = $this->listarEstrutural( $rsRecordSet, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( substr( $rsRecordSet->getCampo('cod_estrutural'),0,1 ) != '9' and $this->getValorPeriodo() < 0 ) {
                    $obErro->setDescricao( 'Valor não pode ser negativo' );
                }

                if ( !$obErro->ocorreu() ) {
                    $obTOrcamentoPrevisaoReceita->setDado( "exercicio"   , $this->getExercicio()     );
                    $obTOrcamentoPrevisaoReceita->setDado( "periodo"     , $this->getPeriodo()       );
                    $obTOrcamentoPrevisaoReceita->setDado( "cod_receita" , $this->getCodigoReceita() );
                    $obTOrcamentoPrevisaoReceita->setDado( "vl_periodo"  , $this->getValorPeriodo()  );
                    $obErro = $obTOrcamentoPrevisaoReceita->inclusao( $boTransacao );
                }
            }

        return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente Previsão Receita
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listar(&$rsLista, $stFiltro = "", $boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrevisaoReceita.class.php" );
        $obTOrcamentoPrevisaoReceita          = new TOrcamentoPrevisaoReceita;

        $stFiltro = "";
        if ( $this->getExercicio() ) {
            $stFiltro .= " AND O.exercicio = '".$this->getExercicio()."' AND ";
        }
        if ( $this->obROrcamentoReceita->obROrcamentoEntidade->obRCGM->getNumCGM() ) {
            $stFiltro .= " UE.numcgm = ".$this->obROrcamentoReceita->obROrcamentoEntidade->obRCGM->getNumCGM()." AND ";
        }
        if ( $this->obROrcamentoReceita->obROrcamentoEntidade->getCodigoEntidade() ) {
            $stFiltro .= " O.cod_entidade = ".$this->obROrcamentoReceita->obROrcamentoEntidade->getCodigoEntidade()." AND ";
        }
        if ( $this->getCodEstruturalDedutora() ) {
            $stFiltro .= " CR.mascara_classificacao ILIKE '".$this->getCodEstruturalDedutora()."' AND ";
        }
        if ( $this->getCodEstruturalInicial() && $this->getCodEstruturalFinal() ) {
            $stFiltro .= " CR.mascara_classificacao BETWEEN '".$this->getCodEstruturalInicial()."' AND '".$this->getCodEstruturalFinal()."' AND ";
        }
        if ( $this->getCodReceitaInicial() && $this->getCodReceitaFinal() ) {
            $stFiltro .= " O.cod_receita BETWEEN ".$this->getCodReceitaInicial()." AND ".$this->getCodReceitaFinal()." AND ";
        }
        if ($stFiltro) {
            $stFiltro = substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
        }

        $stOrdem = " cod_receita ";
        $obErro = $obTOrcamentoPrevisaoReceita->recuperaRelacionamento( $rsLista, $stFiltro, $stOrdem, $obTransacao );

        return $obErro;
    }

    /**
        * Executa um recuperaTodos na classe Persistente Previsão Receita
        * @access Public
        * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
        * @param  String $stOrdem Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function listarPeriodo(&$rsLista, $stFiltro = "", $boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrevisaoReceita.class.php" );
        $obTOrcamentoPrevisaoReceita          = new TOrcamentoPrevisaoReceita;
        $obTransacao = isset($obTransacao) ? $obTransacao : false;
        if ($stFiltro !="") {
            $stFiltro .= "  AND  ";
        }
        if ( $this->getExercicio() ) {
            $stFiltro .= " exercicio = '".$this->getExercicio()."' AND ";
        }
        if ($stFiltro) {
            $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
        }
        $stOrder = " ORDER BY cod_receita, periodo ";
        $obErro  = $obTOrcamentoPrevisaoReceita->recuperaTodos( $rsLista, $stFiltro, $stOrder, $obTransacao );

        return $obErro;
    }

    /**
        * Executa um recuperaEstrutural na classe Persistente Previsão Receita
        * @access Public
        * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function listarEstrutural(&$rsLista, $boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrevisaoReceita.class.php" );
        $obTOrcamentoPrevisaoReceita          = new TOrcamentoPrevisaoReceita;

        $stFiltro;
        if( $this->inExercicio )
            $stFiltro .= " ORE.exercicio = '".$this->inExercicio."' AND ";
        if( $this->inCodigoReceita )
            $stFiltro .= " ORE.cod_receita = ".$this->inCodigoReceita." AND ";
        if ($stFiltro) {
            $stFiltro = " AND".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
        }
        $stOrder = " ORDER BY cod_receita ";
        $obErro = $obTOrcamentoPrevisaoReceita->recuperaEstrutural( $rsLista, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

    /**
        * Limpa os dados da Previsão Receita
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function limparDados($boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrevisaoReceita.class.php" );
        $obTOrcamentoPrevisaoReceita          = new TOrcamentoPrevisaoReceita;

            $obTOrcamentoPrevisaoReceita->setDado( "exercicio"   , $this->getExercicio()     );
            $obTOrcamentoPrevisaoReceita->setDado( "cod_receita" , $this->getCodigoReceita() );
            $obErro = $obTOrcamentoPrevisaoReceita->recuperaLimpaReceita( $rsLista, "", "", $boTransacao );

        return $obErro;
    }
}
