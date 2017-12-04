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
    * Classe de Regra de Negócio Itens
    * Data de Criação   : 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @package URBEM
    * @subpackage Regra

    $Id: ROrcamentoOrgaoOrcamentario.class.php 59612 2014-09-02 12:00:51Z gelson $
    $Revision: 30824 $
    $Name$
    $Autor:$
    $Date: 2008-03-28 14:59:03 -0300 (Sex, 28 Mar 2008) $

    * Casos de uso: uc-02.01.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS . 'Transacao.class.php';
include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoConfiguracao.class.php';

/**
    * Classe de Regra de Negócio Itens
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo Boezzio Paulino
*/
class ROrcamentoOrgaoOrcamentario
{
/**
    * @var Object
    * @access Private
*/
var $obTOrcamentoOrgao;
/**
    * @var String
    * @access Private
*/
var $stMascara;
/**
    * @var Object
    * @access Private
*/
var $obRConfiguracaoOrcamento;
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
var $stNomeOrgao;
/**
    * @var Integer
    * @access Private
*/
var $stDescricao;
/**
    * @var Integer
    * @access Private
*/
var $inNumeroOrgao;

/**
    * @access Public
    * @param Object $valor
*/
function setRConfiguracaoOrcamento($valor) { $this->obRConfiguracaoOrcamento = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara                = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTOrcamentoOrgao($valor) { $this->obTOrcamentoOrgao = $valor;    }
/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao          = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio          = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setNumeroOrgao($valor) { $this->inNumeroOrgao    = $valor;     }
/**
     * @access Public
     * @param String $valor
*/
function setNomeOrgao($valor) { $this->stNomeOrgao    = $valor;     }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodResponsavel($valor) { $this->inCodResponsavel = $valor;     }

/**
    * @access Public
    * @return String
*/
function getMascara() { return $this->stMascara;                          }
/**
    * @access Public
    * @return Object
*/
function getRConfiguracaoOrcamento() { return $this->obRConfiguracaoOrcamento;           }
/**
     * @access Public
     * @return Object
*/
function getTOrcamentoOrgao() { return $this->obTOrcamentoOrgao;    }
/**
     * @access Public
     * @return Object
*/
function getROrgao() { return $this->obROrgao;             }
/**
     * @access Public
     * @return Object
*/
function getTransacao() { return $this->obTransacao;          }
/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;          }
/**
     * @access Public
     * @return Integer
*/
function getNumeroOrgao() { return $this->inNumeroOrgao;        }
/**
     * @access Public
     * @return String
*/
function getNomeOrgao() { return $this->stNomeOrgao;          }
/**
     * @access Public
     * @return Integer
*/
function getCodResponsavel() { return $this->inCodResponsavel;     }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoOrgaoOrcamentario()
{
    $this->setRConfiguracaoOrcamento( new ROrcamentoConfiguracao);
    $this->setTransacao             ( new Transacao             );
    $this->setExercicio             ( Sessao::getExercicio()    );
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php"  );
    $obTOrcamentoOrgao        = new TOrcamentoOrgao;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoOrgao->setDado( "num_orgao"     , $this->getNumeroOrgao()     );
        $obTOrcamentoOrgao->setDado( "exercicio"     , $this->getExercicio()           );
        $obTOrcamentoOrgao->setDado( "nom_orgao"     , $this->getNomeOrgao()           );
        $obTOrcamentoOrgao->setDado( "usuario_responsavel" , $this->getCodResponsavel() );
        $obErro = $obTOrcamentoOrgao->inclusao( $boTransacao );
        if ($obErro->ocorreu()) {
            $obErro->setDescricao('Já existe Orgão Orçamentário cadastrado com o código informado!');
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoOrgao );
    }

    return $obErro;
}

/**
    * Exclui os dados do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php"  );
    $obTOrcamentoOrgao        = new TOrcamentoOrgao;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoOrgao->setDado( "num_orgao"     , $this->getNumeroOrgao()     );
        $obTOrcamentoOrgao->setDado( "exercicio"     , $this->getExercicio()           );
        $obErro = $obTOrcamentoOrgao->exclusao( $boTransacao );
        if(strpos($obErro->getDescricao(),"referenciada pela tabela"))
            $obErro->setDescricao("Esta informação é utilizada em outras partes do sistema.");
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoOrgao );
    }

    return $obErro;
}

/**
    * Executa um recuperaRelacionamento na classe Persistente Item
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "", $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php"  );
    $obTOrcamentoOrgao        = new TOrcamentoOrgao;

    $stFiltro = "";
    if ( $this->getNumeroOrgao() ) {
        $stFiltro .= " AND OO.num_orgao = ".$this->getNumeroOrgao();
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND OO.exercicio = '".$this->getExercicio()."'";
    }
    $obErro = $obTOrcamentoOrgao->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente Item
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar(&$rsLista, $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php"  );
    $obTOrcamentoOrgao        = new TOrcamentoOrgao;

    $obTOrcamentoOrgao->setDado( "exercicio" , $this->getExercicio()       );
    $obTOrcamentoOrgao->setDado( "num_orgao" , $this->getNumeroOrgao() );
    $obErro = $obTOrcamentoOrgao->recuperaRelacionamento( $rsLista, $obTransacao );

    return $obErro;
}

/**
    * Recupera nível da mascara definida na máscara de despesa
    * @access Public
    * @return Object Objeto Erro
*/
function pegarMascara(&$obTOrcamentoOrgaoParametro)
{
    $obErro = $this->obRConfiguracaoOrcamento->consultarConfiguracao();
    $stMascara = $this->obRConfiguracaoOrcamento->getMascDespesa();
    $arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);

    // Grupo O;
    $stMascara = $arMarcara[0];
    $this->setMascara( $stMascara );
    $obTOrcamentoOrgaoParametro->setDado( "stMascara"  , $this->getMascara() );

    return $obErro;
}

/**
    * Recupera nível da mascara definida na máscara de despesa
    * @access Public
    * @return Object Objeto Erro
*/
function buscarMascara()
{
    $this->obRConfiguracaoOrcamento->consultarConfiguracao();
    $stMascara = $this->obRConfiguracaoOrcamento->getMascDespesa();
    $arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);

    // Grupo O;
    $stMascara = $arMarcara[0];
    $this->setMascara( $stMascara );
}

}
