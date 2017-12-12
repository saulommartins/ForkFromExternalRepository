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
    * Classe de regra de negócio Pessoal Classificacao assentamento
    * Data de Criação: 26/05/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.08

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalClassificacaoAssentamento.class.php"  );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalTipoClassificacao.class.php"          );

class RPessoalClassificacaoAssentamento
{
/**
    * @access Private
    * @var Integer
*/
var $inCodClassificacaoAssentamento;
/**
    * @access Private
    * @var String
*/
var $stDescricao;
/**
    * @access Private
    * @var String
*/
var $inCodTipo;
/**
    * @access Private
    * @var Object
*/
var $obTPessoalClassificacaoAssentamento;
/**
    * @access Private
    * @var Object
*/
var $obTPessoalTipoClassificacao;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodClassificacaoAssentamento($valor) { $this->inCodClassificacaoAssentamento       = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDescricao($valor) { $this->stDescricao                          = $valor; }
/**
    * @access Public
    * @param Descricao $Valor
*/
function setCodTipo($valor) { $this->inCodTipo                            = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTransacao($valor) { $this->obTransacao                          = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalClassificacaoAssentamento($valor) { $this->obTPessoalClassificacaoAssentamento  = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalTipoClassificacao($valor) { $this->obTPessoalTipoClassificacao          = $valor; }
/**
    * @access Public
    * @return Integer
*/
function getCodClassificacaoAssentamento() { return $this->inCodClassificacaoAssentamento;       }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;                          }
/**
    * @access Public
    * @return String
*/
function getCodTipo() { return $this->inCodTipo;                            }
/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                          }
/**
    * @access Public
    * @return Object
*/
function getTPessoalClassificacaoAssentamento() { return $this->obTPessoalClassificacaoAssentamento;  }
/**
    * @access Public
    * @return Object
*/
function getTPessoalTipoClassificacao() { return $this->obTPessoalTipoClassificacao;          }

/**
     * Método construtor
     * @access Private
*/
function RPessoalClassificacaoAssentamento()
{
    $this->setTPessoalClassificacaoAssentamento ( new TPessoalClassificacaoAssentamento     );
    $this->setTPessoalTipoClassificacao         ( new TPessoalTipoClassificacao             );
    $this->setTransacao                         ( new Transacao                             );
}

/**
    * Inclusão de dados da Classificacao Assentamento no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirClassificacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalClassificacaoAssentamento->proximoCod( $inCodClassificacaoAssentamento , $boTransacao );
        $this->setCodClassificacaoAssentamento( $inCodClassificacaoAssentamento );
        $this->obTPessoalClassificacaoAssentamento->setDado("cod_classificacao",    $this->getCodClassificacaoAssentamento() );
        $this->obTPessoalClassificacaoAssentamento->setDado("descricao",            $this->getDescricao() );
        $this->obTPessoalClassificacaoAssentamento->setDado("cod_tipo",             $this->getCodTipo()      );
        $obErro = $this->obTPessoalClassificacaoAssentamento->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalClassificacaoAssentamento );

    return $obErro;
}

/**
    * Altaração de dados da Classificacao Assentamento no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarClassificacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalClassificacaoAssentamento->setDado("descricao",            $this->getDescricao()                       );
        $this->obTPessoalClassificacaoAssentamento->setDado("cod_tipo",             $this->getCodTipo()                         );
        $this->obTPessoalClassificacaoAssentamento->setDado("cod_classificacao",    $this->getCodClassificacaoAssentamento()    );
        $obErro = $this->obTPessoalClassificacaoAssentamento->validaAlteracao("", $boTransacao);
        if (!$obErro->ocorreu() )
            $obErro = $this->obTPessoalClassificacaoAssentamento->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalClassificacaoAssentamento );

    return $obErro;
}

/**
    * Exclui dados da Classificacao do Assentamento do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirClassificacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalClassificacaoAssentamento->setDado("cod_classificacao", $this->getCodClassificacaoAssentamento() );
        $obErro = $this->obTPessoalClassificacaoAssentamento->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalClassificacaoAssentamento );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarClassificacao(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $stFiltro = "";

    if( $this->getCodClassificacaoAssentamento() )
        $stFiltro .= " AND ca.cod_classificacao = ".$this->getCodClassificacaoAssentamento()." ";

    if( $this->getDescricao() )
        $stFiltro .= " AND ca.descricao like '%".$this->getDescricao()."%' ";

    if( $this->getCodTipo() )
        $stFiltro .= " AND ca.cod_tipo = ".$this->getCodTipo()." ";

    $obErro = $this->obTPessoalClassificacaoAssentamento->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarClassificacaoGeracaoAssentamento(&$rsRecordSet, $inCod, $comboType, $stOrder = "", $boTransacao = "")
{
    if( $this->getCodClassificacaoAssentamento() )
        $stFiltro .= " AND ca.cod_classificacao = ".$this->getCodClassificacaoAssentamento()." ";
    if( $this->getDescricao() )
        $stFiltro .= " AND ca.descricao like '%".$this->getDescricao()."%' ";
    if( $this->getCodTipo() )
        $stFiltro .= " AND ca.cod_tipo = ".$this->getCodTipo()." ";
    switch ($comboType) {
        case 'matricula':
        case 'cgm':
            if ($inCod) {
                $stFiltro .= " AND contrato.registro = ".$inCod." ";
            }
            break;
        case 'cargo':
            if ($inCod) {
                $stFiltro .= " AND contrato_servidor.cod_cargo = ".$inCod." ";
            }
            break;
        case 'lotacao':
            if ($inCod) {
                $stFiltro .= " AND contrato_servidor_orgao.cod_orgao = ".$inCod." ";
            }
            break;
    }

    $obErro = $this->obTPessoalClassificacaoAssentamento->recuperaPorContrato( $rsRecordSet, $comboType, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recupera na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarTipo(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $obErro = $this->obTPessoalTipoClassificacao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
