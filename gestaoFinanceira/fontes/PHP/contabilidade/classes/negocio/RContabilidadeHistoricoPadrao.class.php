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
    * Classe de Regra de Negócio Histórico Padrão
    * Data de Criação   : 06/11/2004

    * @author Analista : Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-03-07 09:56:31 -0300 (Qua, 07 Mar 2007) $

    * Casos de uso: uc-02.02.03
                    uc-02.02.20
*/

/*
$Log$
Revision 1.11  2007/03/07 12:55:25  rodrigo_sr
Bug #7993#

Revision 1.10  2007/02/23 13:31:19  luciano
#8480#

Revision 1.9  2007/02/12 18:28:45  luciano
#8371#

Revision 1.8  2007/02/06 11:27:47  luciano
#8280#

Revision 1.7  2007/02/06 11:09:14  luciano
#8279#

Revision 1.6  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"  );

class RContabilidadeHistoricoPadrao
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
    * @var Integer
    * @access Private
*/
var $inCodHistorico;
/**
    * @var Integer
    * @access Private
*/
var $inCodHistoricoInclusao;
/**
    * @var String
    * @access Private
*/
var $stNomHistorico;
/**
    * @var Boolean
    * @access Private
*/
var $boComplemento;

/**
    * @var Boolean
    * @access Private
*/
var $boHistoricoInterno;

/**
    * @var String
    * @access Private
*/
var $stFiltroCodHistorico;

/**
    * @var String
    * @access Private
*/
var $stOrdenacao;

/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                   = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio                   = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodHistorico($valor) { $this->inCodHistorico               = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodHistoricoInclusao($valor) { $this->inCodHistoricoInclusao      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setNomHistorico($valor) { $this->stNomHistorico               = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setComplemento($valor) { $this->boComplemento       	       = $valor; }

/**
     * @access Public
     * @param Boolean $valor
*/
function setHistoricoInterno($valor) { $this->boHistoricoInterno       = $valor; }

/**
     * @access Public
     * @param Integer $valor
*/
function setFiltroCodHistorico($valor) { $this->stFiltroCodHistorico               = $valor; }

/**
     * @access Public
     * @param Integer $valor
*/
function setOrdenacao($valor) { $this->stOrdenacao               = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getTransacao() { return $this->obTransacao;                   }
/**
     * @access Public
     * @param String $valor
*/
function getExercicio() { return $this->stExercicio;                   }
/**
     * @access Public
     * @param Integer $valor
*/
function getCodHistorico() { return $this->inCodHistorico;                }
/**
     * @access Public
     * @param Integer $valor
*/
function getCodHistoricoInclusao() { return $this->inCodHistoricoInclusao;        }
/**
     * @access Public
     * @param String $valor
*/
function getNomHistorico() { return $this->stNomHistorico;                }
/**
     * @access Public
     * @param Boolean $valor
*/
function getComplemento() { return $this->boComplemento;			 	}

/**
     * @access Public
     * @param Boolean $valor
*/
function getHistoricoInterno() { return $this->boHistoricoInterno;		 	}

/**
     * @access Public
     * @param Integer $valor
*/
function getFiltroCodHistorico() { return $this->stFiltroCodHistorico;                }

/**
     * @access Public
     * @param Integer $valor
*/
function getOrdenacao() { return $this->stOrdenacao;                }

/**
    * Método Construtor
    * @access Private
*/
function RContabilidadeHistoricoPadrao()
{
    $this->setExercicio              	( Sessao::getExercicio()                  );
    $this->setTransacao              	( new Transacao                       );
}

/**
    * Busca proximo CodHistorico do Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
*/
function proximoCodHistorico($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeHistoricoContabil.class.php" );
    $obTHistoricoContabil       = new TContabilidadeHistoricoContabil;

    $obTHistoricoContabil->proximoCod( $inCodHistoricoInclusao , $boTransacao );
    $this->setCodHistoricoInclusao( $inCodHistoricoInclusao );
}

/**
    * Busca o último código histórico do banco de dados para a inclusão (entre 1 e 799)
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
*/
function recuperaCodHistoricoInclusao($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeHistoricoContabil.class.php" );
    $obTHistoricoContabil = new TContabilidadeHistoricoContabil;

    $obErro = $obTHistoricoContabil->recuperaHistoricosInclusao( $inCodHistoricoInclusao , $boTransacao );

    if (!$obErro->ocorreu()) {
        $inCodHistoricoInclusao = ++$inCodHistoricoInclusao->arElementos[0]['cod_historico'];

        if ($inCodHistoricoInclusao >= 800) {
            $obErro->setDescricao("Já foram cadastrados todos os códigos possíveis!");
        } else {
            $this->setCodHistoricoInclusao( $inCodHistoricoInclusao );
        }

    }

    return $obErro;
}

/**
    * Salva Historico no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeHistoricoContabil.class.php" );
    $obTHistoricoContabil       = new TContabilidadeHistoricoContabil;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obTHistoricoContabil->setDado( "exercicio"     , $this->getExercicio()    );
        $obTHistoricoContabil->setDado( "nom_historico" , $this->getNomHistorico() );
        $obTHistoricoContabil->setDado( "complemento"   , $this->getComplemento()  );

        if ( $this->getCodHistorico() ) {

            $obTHistoricoContabil->setDado("cod_historico", $this->getCodHistorico() );
            $this->listarNomeIgual($rsHistorico);

                    if($rsHistorico->getNumLinhas()<=0)
                        $obErro = $obTHistoricoContabil->alteracao( $boTransacao );
                    else
                        $obErro->setDescricao("Já existe uma descrição semelhante a informada!");

        } else {
            $this->listarNomeIgual($rsHistorico);

            $this->setCodHistorico( $this->getCodHistoricoInclusao() );

            $stFiltro = " WHERE cod_historico = ".$this->getCodHistorico();
            $obErro = $obTHistoricoContabil->recuperaTodos($rsHistoricoContabil, $stFiltro,'',$boTransacao);
            if ( !$obErro->ocorreu() ) {
                if ( $rsHistoricoContabil->eof() ) {

            $obTHistoricoContabil->setDado("cod_historico", $this->getCodHistorico() );

                    if($rsHistorico->getNumLinhas()<=0)
                        $obErro = $obTHistoricoContabil->inclusao( $boTransacao );
                    else
                        $obErro->setDescricao("Já existe uma descrição semelhante a informada!");

                }else
                    $obErro->setDescricao("Código ".$this->getCodHistorico()." já cadastrado!");
            }

        }
       $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTHistoricoContabil );
    }

    return $obErro;
}

/**
    * Exclui dados de Calendario do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeHistoricoContabil.class.php" );
    $obTHistoricoContabil       = new TContabilidadeHistoricoContabil;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obTHistoricoContabil->setDado("cod_historico", $this->getCodHistorico() );
        $obTHistoricoContabil->setDado("exercicio"    , $this->getExercicio()    );

        $this->consultar();
        if ($this->getHistoricoInterno() == 't') {
            $obErro->setDescricao('Histórico não pode ser excluído porque está sendo utilizado.');
        } elseif (!$obErro->ocorreu()) {
            $obErro = $obTHistoricoContabil->exclusao( $boTransacao );
            if ($obErro->ocorreu()) {
                $obErro->setDescricao('Histórico não pode ser excluído porque está sendo utilizado.');
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTHistoricoContabil );
    }

    return $obErro;
}
/**
    * Lista todos os Calendarios de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "cod_historico", $obTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeHistoricoContabil.class.php" );
    $obTHistoricoContabil       = new TContabilidadeHistoricoContabil;

    $stFiltro = "";

    if( $this->getCodHistorico() )
        $stFiltro .= " AND cod_historico = ". $this->getCodHistorico();
    if( $this->getExercicio() )
        $stFiltro .= " AND exercicio = '". $this->getExercicio() ."' ";
    if( $this->getNomHistorico() )
        $stFiltro .= " AND LOWER( nom_historico ) LIKE LOWER('%". $this->getNomHistorico() ."%') ";
    if( $this->getFiltroCodHistorico() )
        $stFiltro .= "".$this->getFiltroCodHistorico()."";
    
    $stFiltro = ($stFiltro)?' WHERE cod_historico IS NOT NULL AND cod_historico != 1 '.$stFiltro:'';

    $obErro = $obTHistoricoContabil->recuperaTodos( $rsLista, $stFiltro, $stOrder, $obTransacao );
    
    return $obErro;
}

/**
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarRelatorio(&$rsLista, $stOrder = "cod_historico", $obTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeHistoricoContabil.class.php" );
    $obTHistoricoContabil       = new TContabilidadeHistoricoContabil;

    $stFiltro = "";

    if( $this->getCodHistorico() )
        $stFiltro .= " AND cod_historico = ". $this->getCodHistorico();
    if( $this->getExercicio() )
        $stFiltro .= " AND exercicio = '". $this->getExercicio() ."' ";
    if( $this->getNomHistorico() )
        $stFiltro .= " AND LOWER( nom_historico ) LIKE LOWER('%". $this->getNomHistorico() ."%') ";
    if( $this->getFiltroCodHistorico() )
        $stFiltro .= "".$this->getFiltroCodHistorico()."";
    if ($this->getComplemento()) {
        if( $this->getComplemento() != 'ambos')
            $stFiltro .= " AND complemento = ".$this->getComplemento();
    }
    $stFiltro = ($stFiltro)?' WHERE cod_historico IS NOT NULL '.$stFiltro:'';

    $obErro = $obTHistoricoContabil->recuperaTodos( $rsLista, $stFiltro, $stOrder, $obTransacao );
    
    return $obErro;
}


/**
    * Lista todos os Calendarios de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNomeIgual(&$rsLista, $stOrder = "cod_historico", $obTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeHistoricoContabil.class.php" );
    $obTHistoricoContabil       = new TContabilidadeHistoricoContabil;

    $stFiltro = "";

    if( $this->getCodHistorico() )
        $stFiltro .= " AND cod_historico <> ". $this->getCodHistorico();
    if( $this->getExercicio() )
        $stFiltro .= " AND exercicio = '". $this->getExercicio() ."' ";
    if( $this->getNomHistorico() )
        $stFiltro .= " AND LOWER( nom_historico ) LIKE LOWER('". $this->getNomHistorico() ."') ";

    $stFiltro = ($stFiltro)?' WHERE cod_historico IS NOT NULL '.$stFiltro:'';

    $obErro = $obTHistoricoContabil->recuperaTodos( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeHistoricoContabil.class.php" );
    $obTHistoricoContabil       = new TContabilidadeHistoricoContabil;

    $obTHistoricoContabil->setDado( "cod_historico" , $this->getCodHistorico() );
    $obTHistoricoContabil->setDado( "exercicio"     , $this->getExercicio()    );

    $obErro = $obTHistoricoContabil->recuperaPorChave( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->setNomHistorico      ( $rsRecordSet->getCampo("nom_historico") );
        $this->setComplemento       ( $rsRecordSet->getCampo("complemento") );
        $this->setHistoricoInterno  ( $rsRecordSet->getCampo("historico_interno") );
    }

    return $obErro;
}

}
