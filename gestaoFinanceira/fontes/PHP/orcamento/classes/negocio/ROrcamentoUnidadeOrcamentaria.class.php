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

    $Revision: 30824 $
    $Name$
    $Autor:$
    $Date: 2008-04-07 10:06:52 -0300 (Seg, 07 Abr 2008) $

    * Casos de uso: uc-02.01.02
                    uc-02.01.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"               );
include_once ( CAM_GF_ORC_NEGOCIO        ."ROrcamentoOrgaoOrcamentario.class.php"      );
include_once ( CAM_GA_ADM_NEGOCIO        ."RUnidade.class.php"                );
include_once ( CAM_GF_ORC_NEGOCIO        ."ROrcamentoConfiguracao.class.php"  );

/**
    * Classe de Regra de Negócio Itens
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo Boezzio Paulino
*/
class ROrcamentoUnidadeOrcamentaria
{
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoOrgaoOrcamentario;
/**
    * @var Object
    * @access Private
*/
var $obRUnidade;
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
    * @var Integer
    * @access Private
*/
var $inNumeroUnidade;
/**
    * @var String
    * @access Private
*/
var $stMascara;
/**
    * @var String
    * @access Private
*/
var $stDescricao;
/**
    * @var Objeto
    * @access Private
*/
var $obRConfiguracaoOrcamento;
/**
    * @var String
    * @access Private
*/
var $stNomUnidade;
/**
    * @var Integer
    * @access Private
*/
var $inCodResponsavel;
/**
    * @access Public
    * @param Object $valor
*/
function setRConfiguracaoOrcamento($valor) { $this->obRConfiguracaoOrcamento = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRUnidade($valor) { $this->obRUnidade            = $valor;  }
/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoOrgaoOrcamentario($valor) { $this->obROrcamentoOrgaoOrcamentario = $valor;   }
/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao          = $valor;   }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio          = $valor;   }
/**
     * @access Public
     * @param Integer $valor
*/
function setNumeroUnidade($valor) { $this->inNumeroUnidade      = $valor;   }
/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara            = $valor;   }
/**
    * @access Public
    * @param String $valor
*/
function setNomUnidade($valor) { $this->stNomUnidade         = $valor;   }
/**
    * @access Integer
    * @param String $valor
*/
function setCodResponsavel($valor) { $this->inCodResponsavel     = $valor;   }

/**
    * @access Public
    * @return Object
*/
function getConfiguracaoOrcamento() { return $this->obRConfiguracaoOrcamento;      }
/**
     * @access Public
     * @return Object
*/
function getROrcamentoOrgaoOrcamentario() { return $this->obROrcamentoOrgaoOrcamentario;   }
/**
     * @access Public
     * @return Object
*/
function getRUnidade() { return $this->obRUnidade;             }
/**
     * @access Public
     * @return Object
*/
function getTransacao() { return $this->obTransacao;            }
/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;            }
/**
     * @access Public
     * @return Integer
*/
function getNumeroUnidade() { return $this->inNumeroUnidade;        }
/**
    * @access Public
    * @return String
*/
function getMascara() { return $this->stMascara;              }
/**
    * @access Public
    * @return String
*/
function getNomUnidade() { return $this->stNomUnidade;           }
/**
    * @access Integer
    * @return String
*/
function getCodResponsavel() { return $this->inCodResponsavel;       }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoUnidadeOrcamentaria()
{
    $this->setRConfiguracaoOrcamento      ( new ROrcamentoConfiguracao       );
    $this->setROrcamentoOrgaoOrcamentario ( new ROrcamentoOrgaoOrcamentario  );
    $this->setTransacao                   ( new Transacao                    );
    $this->setExercicio                   ( Sessao::getExercicio()           );
}

/**
    * Inclui Unidade Orcamentaria no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoUnidade.class.php"       );
    $obTOrcamentoUnidade          = new TOrcamentoUnidade;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoUnidade->setDado( "exercicio"     , $this->getExercicio()                                 );
        $obTOrcamentoUnidade->setDado( "num_unidade"   , $this->getNumeroUnidade()                             );
        $obTOrcamentoUnidade->setDado( "num_orgao"     , $this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() );
        $obTOrcamentoUnidade->setDado( "nom_unidade"   , $this->getNomUnidade()                                );
        $obTOrcamentoUnidade->setDado( "usuario_responsavel"   , $this->getCodResponsavel()                    );
        $obErro = $obTOrcamentoUnidade->inclusao( $boTransacao );
        if ($obErro->ocorreu()) {
            $obErro->setDescricao("Número ".$this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao().".".$this->getNumeroUnidade()." já cadastrado!");
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoUnidade );
    }

    return $obErro;
}

/**
    * Altera Unidade Orcamentaria
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoUnidade.class.php"       );
    $obTOrcamentoUnidade          = new TOrcamentoUnidade;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoUnidade->setDado( "num_unidade"   , $this->getNumeroUnidade()                                               );
        $obTOrcamentoUnidade->setDado( "exercicio"     , $this->getExercicio()                                                   );
        $obTOrcamentoUnidade->setDado( "num_orgao"     , $this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()                  );
        $obTOrcamentoUnidade->setDado( "nom_unidade"   , $this->getNomUnidade()                                );
        $obTOrcamentoUnidade->setDado( "usuario_responsavel"   , $this->getCodResponsavel()                    );
        $obErro = $obTOrcamentoUnidade->alteracao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoUnidade );
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
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoUnidade.class.php"       );
    $obTOrcamentoUnidade          = new TOrcamentoUnidade;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    ini_set("display_errors",0);
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoUnidade->setDado( "num_unidade" , $this->getNumeroUnidade()                     );
        $obTOrcamentoUnidade->setDado( "exercicio"   , $this->getExercicio()                         );
        $obTOrcamentoUnidade->setDado( "num_orgao"   , $this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() );
        $obErro = $obTOrcamentoUnidade->exclusao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoUnidade );
    }
    if(strpos($obErro->getDescricao(),"referenciada pela tabela"))
        $obErro->setDescricao("Esta informação é utilizada em outras partes do sistema.");

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
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoUnidade.class.php"       );
    $obTOrcamentoUnidade          = new TOrcamentoUnidade;

    $this->pegarMascara($obTOrcamentoUnidade);
    $stFiltro = "";
    if ( $this->getNumeroUnidade() ) {
        $stFiltro .= " unidade.num_unidade = ".$this->getNumeroUnidade()." AND ";
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " unidade.exercicio = '" . $this->getExercicio() . "' AND ";
    }
    if ( $this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() ) {
        $stFiltro .= " unidade.num_orgao = ".$this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    $obErro = $obTOrcamentoUnidade->recuperaMascarado( $rsLista, $stFiltro, $stOrder, $obTransacao );

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
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoUnidade.class.php"       );
    $obTOrcamentoUnidade          = new TOrcamentoUnidade;

    $stFiltro = "";
    if ( $this->getNumeroUnidade() ) {
        $stFiltro .= " num_unidade = ".$this->getNumeroUnidade() . " AND ";
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " unidade.exercicio = '".$this->getExercicio()."' AND ";
    }
    if ( $this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() ) {
        $stFiltro .= " unidade.num_orgao = ".$this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() . " AND ";
    }
    if ($stFiltro != '') {
        $stFiltro = ' WHERE ' . substr($stFiltro,0,-4);
    }
    $obErro = $obTOrcamentoUnidade->recuperaRelacionamento( $rsLista, $stFiltro, '', $obTransacao );

    return $obErro;
}

/**
    * Recupera nível da mascara definida na máscara de despesa
    * @access Public
    * @return Object Objeto Erro
*/
function pegarMascara(&$obTOrcamentoUnidadeParametro)
{
    $obErro = new RecordSet;
    $stMascara = $this->obRConfiguracaoOrcamento->consultarConfiguracaoEspecifica('masc_despesa');
    $arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);

    // Grupo U;
    $stMascara = $arMarcara[1];

    $this->setMascara( $stMascara );
    $obTOrcamentoUnidadeParametro->setDado( "stMascara"  , $this->getMascara() );

    $obTOrcamentoUnidadeParametro->setDado( "stMascaraOrgao"  , $arMarcara[0] );
    $obTOrcamentoUnidadeParametro->setDado( "stMascaraUnidade"  , $arMarcara[1] );

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

    // Grupo U;
    $stMascara = $arMarcara[1];
    $this->setMascara( $stMascara );
}

}
