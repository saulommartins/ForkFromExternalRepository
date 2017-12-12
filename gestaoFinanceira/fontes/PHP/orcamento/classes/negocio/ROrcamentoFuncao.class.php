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
    * Classe de Regra de Negócio Função Orçamento
    * Funções orçamentárias que fazem parte da classificação funcional-programática da despesa
    * Data de Criação   : 14/07/2004

    * @author Analista: Jorge B.
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @package URBEM
    * @subpackage Regra

    * $Id: ROrcamentoFuncao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"     );
include_once (CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );

/**
    * Classe de Regra de Negócio Função Orçamento
    * Data de Criação   : 14/07/2004

    * @author Analista: Jorge B.
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @package URBEM
    * @subpackage Regra
*/
class ROrcamentoFuncao
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
    public $inCodigoFuncao;

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
    * @var Objeto
    * @access Private
    */
    public $obTransacao;

    /**
         * @access Public
         * @param Object $valor
    */
    public function setTransacao($valor) { $this->obTransacao      = $valor; }

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
    public function setCodigoFuncao($valor) { $this->inCodigoFuncao = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setExercicio($valor) { $this->inExercicio = $valor;    }

    /**
    * @access Public
    * @return Object
    */
    public function getRConfiguracaoOrcamento() { return $this->obRConfiguracaoOrcamento;      }

    /**
    * @access Public
    * @return String
    */
    public function getDescricao() { return $this->stDescricao;    }

    /**
         * @access Public
         * @return Object
    */
    public function getTransacao() { return $this->obTransacao;              }

    /**
    * @access Public
    * @return String
    */
    public function getMascara() { return $this->stMascara;      }

    /**
    * @access Public
    * @return Integer
    */
    public function getCodigoFuncao() { return $this->inCodigoFuncao; }

    /**
    * @access Public
    * @return Integer
    */
    public function getExercicio() { return $this->inExercicio;    }

    /**
    * Método Construtor
    * @access Private
    */
    public function ROrcamentoFuncao()
    {
        $this->setExercicio               ( Sessao::getExercicio()         );
        $this->setTransacao               ( new Transacao              );
        $this->setRConfiguracaoOrcamento  ( new ROrcamentoConfiguracao );
    }

    /**
    * Cadastra funções orçamentarias que fazem parte da classificação funcional-programática da despesa
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    */
    public function incluir($boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoFuncao.class.php" );
        $obTFuncao                  = new TOrcamentoFuncao;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $stFiltro = " WHERE cod_funcao = " . $this->getCodigoFuncao();
            $stFiltro.= "   AND exercicio  = '" . $this->getExercicio() . "' ";
            $obErro = $obTFuncao->recuperaTodos($rsFuncao, $stFiltro,'',$boTransacao);
            if ( !$obErro->ocorreu() ) {
                if ( $rsFuncao->eof() ) {
            $obTFuncao->setDado( "exercicio"  , $this->getExercicio()    );
            $obTFuncao->setDado( "cod_funcao" , $this->getCodigoFuncao() );
            $obTFuncao->setDado( "descricao"  , $this->getDescricao()    );
            $obErro = $obTFuncao->inclusao( $boTransacao );
            } else {
                    $obErro->setDescricao("Código ".$this->getCodigoFuncao()." já cadastrado!");
                }
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFuncao );
            }
    }

    return $obErro;
}

    /**
    * Altera funções orçamentarias que fazem parte da classificação funcional-programática da despesa
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    */
    public function alterar($boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoFuncao.class.php" );
        $obTFuncao                  = new TOrcamentoFuncao;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTFuncao->setDado( "exercicio"  , $this->getExercicio()    );
            $obTFuncao->setDado( "cod_funcao" , $this->getCodigoFuncao() );
            $obTFuncao->setDado( "descricao"  , $this->getDescricao()    );
            $obErro = $obTFuncao->alteracao( $boTransacao );
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFuncao );
        }

        return $obErro;
    }

    /**
    * Exclui funções orçamentarias que fazem parte da classificação funcional-programática da despesa
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    */
    public function excluir($boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoFuncao.class.php" );
        $obTFuncao                  = new TOrcamentoFuncao;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTFuncao->setDado( "exercicio"  , $this->getExercicio()       );
            $obTFuncao->setDado( "cod_funcao" , $this->getCodigoFuncao()    );
            $obErro = $obTFuncao->exclusao( $boTransacao );
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFuncao );
        }

        return $obErro;
    }

    /**
    * Executa um recuperaPorChave na classe Persistente Orçamento Função
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function consultar($boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoFuncao.class.php" );
        $obTFuncao                  = new TOrcamentoFuncao;

        $obTFuncao->setDado( "exercicio"  , $this->getExercicio()       );
        $obTFuncao->setDado( "cod_funcao" , $this->getCodigoFuncao()    );

        $this->setMascara( $_POST['stMascara'] );
        $obTFuncao->setDado( "stMascara"  , $this->getMascara() );

        $obErro = $obTFuncao->recuperaPorChave( $rsListaFuncao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setDescricao( $rsListaFuncao->getCampo( "descricao" ) );
        }

        return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente Orçamento Função
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listar(&$rsListaFuncao, $stFiltro = "", $boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoFuncao.class.php" );
        $obTFuncao                  = new TOrcamentoFuncao;

        $this->pegarMascara($obTFuncao);
        $stFiltro = "";
        if ( $this->getExercicio() ) {
            $stFiltro .= " exercicio = '".$this->getExercicio()."' AND ";
        }
        if ( $this->getCodigoFuncao() ) {
            $stFiltro .= " cod_funcao = ".$this->getCodigoFuncao()." AND ";
        }
        if ( $this->getDescricao() ) {
            $stFiltro .= " UPPER( descricao ) like UPPER( '%".$this->getDescricao()."%' ) AND ";
        }
        if ($stFiltro) {
            $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
        }
        $stOrdem = " ORDER BY cod_funcao ";
        $obErro = $obTFuncao->recuperaMascarado( $rsListaFuncao , $stFiltro ,$stOrdem ,$boTransacao );

        return $obErro;
    }

    /**
    * Recupera mascara da configuração
    * @access Public
    */
    public function pegarMascara(&$obTFuncaoParametro)
    {
        $obErro = new Erro;
        $stMascara = $this->obRConfiguracaoOrcamento->consultarConfiguracaoEspecifica('masc_despesa');
        $arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);

        // Grupo U;
        $stMascara = $arMarcara[2];
        $this->setMascara( $stMascara );
        $obTFuncaoParametro->setDado( "stMascara"  , $this->getMascara() );

        return $obErro;
    }
}
