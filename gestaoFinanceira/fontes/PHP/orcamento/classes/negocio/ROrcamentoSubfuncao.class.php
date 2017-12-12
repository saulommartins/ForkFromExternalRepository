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
    * Classe de Regra de Negócio Sub-função Orçamento
    * Funções orçamentárias que fazem parte da classificação funcional-programática da despesa
    * Data de Criação   : 15/07/2004

    * @author Analista: Jorge B.
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @package URBEM
    * @subpackage Regra

    $Id: ROrcamentoSubfuncao.class.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2008-04-07 10:06:52 -0300 (Seg, 07 Abr 2008) $

    * Casos de uso: uc-02.01.03
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"        );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"   );

/**
* Classe de Regra de Negócio Sub-função Orçamento
* Data de Criação   : 15/07/2004
* @author Analista: Jorge B.
* @author Desenvolvedor: Roberto Pawelski Rodrigues
*/
class ROrcamentoSubfuncao
{
    /**
    * @var String
    * @access Private
    */
    public $stDescricao;

    /**
    * @var String
    * @access Private
    */
    public $stMascara;

    /**
    * @var Integer
    * @access Private
    */
    public $inCodigoSubFuncao;

    /**
    * @var Integer
    * @access Private
    */
    public $inExercicio;

    /**
    * @var Objeto
    * @access Private
    */
    public $obRConfiguracaoOrcamento;

    /**
        * @var Object
        * @access Private
    */
    public $obTransacao;

    /**
         * @access Public
         * @param Object $valor
    */
    public function setTransacao($valor) { $this->obTransacao               = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setRConfiguracaoOrcamento($valor) { $this->obRConfiguracaoOrcamento = $valor; }

    /**
    * @access Public
    * @param String $valor
    */
    public function setDescricao($valor) { $this->stDescricao   = $valor;  }

    /**
    * @access Public
    * @param String $valor
    */
    public function setMascara($valor) { $this->stMascara     = $valor;  }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setCodigoSubFuncao($valor) { $this->inCodigoSubFuncao = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setExercicio($valor) { $this->inExercicio = $valor;    }

    /**
         * @access Public
         * @return Object
    */
    public function getTransacao() { return $this->obTransacao;              }

    /**
    * @access Public
    * @return Object
    */
    public function getConfiguracaoOrcamento() { return $this->obRConfiguracaoOrcamento;      }

    /**
    * @access Public
    * @return String
    */
    public function getDescricao() { return $this->stDescricao;    }

    /**
    * @access Public
    * @return String
    */
    public function getMascara() { return $this->stMascara;      }

    /**
    * @access Public
    * @return Integer
    */
    public function getCodigoSubFuncao() { return $this->inCodigoSubFuncao; }

    /**
    * @access Public
    * @return Integer
    */
    public function getExercicio() { return $this->inExercicio;    }

    /**
    * Método Construtor
    * @access Private
    */
    public function ROrcamentoSubfuncao()
    {
        $this->setExercicio               ( Sessao::getExercicio()         );
        $this->setRConfiguracaoOrcamento  ( new ROrcamentoConfiguracao );
        $this->setTransacao               ( new Transacao              );
    }

    /**
    * Altera sub-funções orçamentarias que fazem parte da classificação funcional-programática da despesa
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    */
    public function incluir($boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSubfuncao.class.php" );
        $obTSubFuncao               = new TOrcamentoSubfuncao;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $stFiltro = " WHERE cod_subfuncao = ".$this->getCodigoSubFuncao();
            $stFiltro.= "   AND exercicio = '" . $this->getExercicio() . "' ";
            $obErro = $obTSubFuncao->recuperaTodos($rsFuncao, $stFiltro,'',$boTransacao);
            if ( !$obErro->ocorreu() ) {
                if ( $rsFuncao->eof() ) {
                    $obTSubFuncao->setDado("cod_subfuncao" , $this->getCodigoSubFuncao() );
                    $obTSubFuncao->setDado( "exercicio"    , $this->getExercicio()       );
                    $obTSubFuncao->setDado( "descricao"    , $this->getDescricao()       );
                    $obErro = $obTSubFuncao->inclusao( $boTransacao );
                } else {
                    $obErro->setDescricao("Código ".$this->getCodigoSubFuncao()." já cadastrado!");
                }
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTSubFuncao );
            }
        }

        return $obErro;
    }

    /**
    * Altera sub-funções orçamentarias que fazem parte da classificação funcional-programática da despesa
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    */
    public function alterar($boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSubfuncao.class.php" );
        $obTSubFuncao               = new TOrcamentoSubfuncao;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTSubFuncao->setDado( "exercicio"    , $this->getExercicio()       );
            $obTSubFuncao->setDado("cod_subfuncao" , $this->getCodigoSubFuncao() );
            $obTSubFuncao->setDado( "descricao"    , $this->getDescricao()       );
            $obErro = $obTSubFuncao->alteracao( $boTransacao );
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTSubFuncao );
        }

        return $obErro;
    }

    /**
    * Exclui sub-funções orçamentarias que fazem parte da classificação funcional-programática da despesa
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    */
    public function excluir($boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSubfuncao.class.php" );
        $obTSubFuncao               = new TOrcamentoSubfuncao;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTSubFuncao->setDado( "exercicio"  , $this->getExercicio()       );
            $obTSubFuncao->setDado( "cod_subfuncao" , $this->getCodigoSubFuncao()    );
            $obErro = $obTSubFuncao->exclusao( $boTransacao );
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTSubFuncao );
        }

        return $obErro;
    }

    /**
    * Executa um recuperaPorChave na classe Persistente Orçamento Sub-função
    * @access Public
    * @param  Object $$rsListaSubFuncao Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function consultar($boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSubfuncao.class.php" );
        $obTSubFuncao               = new TOrcamentoSubfuncao;

        $obTSubFuncao->setDado( "exercicio"  , $this->getExercicio()       );
        $obTSubFuncao->setDado( "cod_subfuncao" , $this->getCodigoSubFuncao()    );

        $this->setMascara( $_POST['stMascara'] );
        $obTSubFuncao->setDado( "stMascara"  , $this->getMascara() );

        $obErro = $obTSubFuncao->recuperaPorChave( $rsListaSubFuncao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setDescricao( $rsListaSubFuncao->getCampo( "descricao" ) );
        }

        return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente Orçamento Sub-função
    * @access Public
    * @param  Object $$rsListaSubFuncao Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listar(&$rsListaSubFuncao, $stFiltro = "", $boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSubfuncao.class.php" );
        $obTSubFuncao               = new TOrcamentoSubfuncao;

        $this->pegarMascara($obTSubFuncao);
        $stFiltro = "";
        if ( $this->getExercicio() ) {
            $stFiltro .= " exercicio = '" . $this->getExercicio() . "' AND ";
        }
        if ( $this->getCodigoSubFuncao() ) {
            $stFiltro .= " cod_subfuncao = ".$this->getCodigoSubFuncao()." AND ";
        }
        if ( $this->getDescricao() ) {
            $stFiltro .= " UPPER( descricao ) like UPPER( '%".$this->getDescricao()."%' ) AND ";
        }
        if ($stFiltro) {
            $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
        }
        $stOrdem = " ORDER BY cod_subfuncao ";
        $obErro = $obTSubFuncao->recuperaMascarado( $rsListaSubFuncao , $stFiltro ,$stOrdem ,$boTransacao );

        return $obErro;
    }

    /**
    * Recupera mascara da configuração
    * @access Public
    */
    public function pegarMascara(&$obTSubFuncaoParametro)
    {
        $obErro = new Erro;
        $stMascara = $this->obRConfiguracaoOrcamento->consultarConfiguracaoEspecifica('masc_despesa');
        $arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);

        // Grupo F;
        $stMascara = $arMarcara[3];
        $this->setMascara( $stMascara );
        $obTSubFuncaoParametro->setDado( "stMascara"  , $this->getMascara() );

        return $obErro;
    }
}
