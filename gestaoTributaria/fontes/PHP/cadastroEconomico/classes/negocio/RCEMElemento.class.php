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
    * Classe de regra de negócio para Elementos
    * Data de Criação: 20/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMElemento.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.05
*/

/*
$Log$
Revision 1.17  2007/05/14 20:35:45  dibueno
Alterações para possibilitar a emissao do alvará diverso

Revision 1.16  2007/02/26 19:41:24  cassiano
Bug #8429#

Revision 1.15  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMElemento.class.php"           );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMElementoAtividade.class.php"  );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMElementoTipoLicencaDiversa.class.php"  );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoElemento.class.php"   );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMBaixaElemento.class.php"      );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoElemCadEconomicoValor.class.php");
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );

class RCEMElemento
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoElemento;
/**
    * @access Private
    * @var String
*/
var $stNomeElemento;
/**
    * @access Private
    * @var String
*/
var $stMotivo;
/**
    * @access Private
    * @var Object
*/
var $roRCEMAtividade;
/**
    * @access Private
    * @var Object
*/
var $roRCEMTipoLicencaDiversa;
/**
    * @access Private
    * @var Object
*/
var $obRCadastroDinamico;
/**
    * @access Private
    * @var Object
*/
var $obTCEMElemento;
/**
    * @access Private
    * @var Object
*/
var $obTCEMElementoTipoLicencaDiversa;
/**
    * @access Private
    * @var Object
*/
var $obTCEMElementoAtividade;
/**
    * @access Private
    * @var Object
*/
var $obTCEMBaixaElemento;
/**
    * @access Private
    * @var Intger
*/
var $inOcorrenciaElemento;
/**
    * @access Private
    * @var Array
*/
var $arArrayElemento;

function setCodigoElemento($valor) { $this->inCodigoElemento = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomeElemento($valor) { $this->stNomeElemento = $valor;   }
/**
    * @access Public
    * @param String $valor
*/
function setMotivo($valor) { $this->stMotivo = $valor;         }
/**
    * @access Public
    * @param Integer $valor
*/
function setOcorrenciaElemento($valor) { $this->inOcorrenciaElemento = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setArrayElemento($valor) { $this->arArrayElemento = $valor; }

/**
     * @access Public
     * @return Integer
*/
function getCodigoElemento() { return $this->inCodigoElemento;          }
/**
     * @access Public
     * @return String
*/
function getNomeElemento() { return $this->stNomeElemento;            }
/**
     * @access Public
     * @return String
*/
function getMotivo() { return $this->stMotivo;                  }
/**
     * @access Public
     * @return Integer
*/
function getOcorrenciaElemento() { return $this->inOcorrenciaElemento;      }
/**
     * @access Public
     * @return Integer
*/
function getArrayElemento() { return $this->arArrayElemento;           }

/**
    * Metodo construtor
    * @access Private
*/

function RCEMElemento(&$obRCEMAtividade)
{
    $this->obTCEMElemento          = new TCEMElemento;
    $this->obTCEMElementoAtividade = new TCEMElementoAtividade;
    $this->obTCEMBaixaElemento     = new TCEMBaixaElemento;
    $this->roCEMAtividade          = &$obRCEMAtividade;
    $this->obTransacao             = new Transacao;
    $this->obTCEMElementoTipoLicencaDiversa = new TCEMElementoTipoLicencaDiversa;
    $this->obRCadastroDinamico  = new RCadastroDinamico;
    $this->obRCadastroDinamico->setPersistenteValores( new TCEMAtributoElemCadEconomicoValor );
    $this->obRCadastroDinamico->setCodCadastro( 5 );
}

/**
    * Inclui os dados setados na tabela de Elemento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirElemento($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->validaNomeElemento( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obRCadastroDinamico->setPersistenteAtributos( new TCEMAtributoElemento() );
            $obErro = $this->obTCEMElemento->proximoCod( $this->inCodigoElemento, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCEMElemento->setDado( "cod_elemento" , $this->inCodigoElemento );
                $this->obTCEMElemento->setDado( "nom_elemento" , $this->stNomeElemento   );
                $obErro = $this->obTCEMElemento->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    //O Restante dos valores vem setado da página de processamento
                    $arChaveAtributo =  array( "cod_elemento" => $this->inCodigoElemento );
                    $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                    $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMElemento );

    return $obErro;
}

/**
    * Altera os dados setados na tabela de Elemento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarElemento($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->validaNomeElemento( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMElemento->setDado( "nom_elemento" , $this->stNomeElemento   );
            $this->obTCEMElemento->setDado( "cod_elemento" , $this->inCodigoElemento );
            $obErro = $this->obTCEMElemento->alteracao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obRCadastroDinamico->setPersistenteAtributos( new TCEMAtributoElemento() );
                //O Restante dos valores vem setado da página de processamento
                $arChaveAtributo =  array( "cod_elemento" => $this->inCodigoElemento );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMElemento );

    return $obErro;
}

/**
    * Exclui o Elemento selecionado do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirElemento($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTCEMAtributoElemCadEconomicoValor = new TCEMAtributoElemCadEconomicoValor();
        $stFiltro  = " WHERE \n";
        $stFiltro .= "     cod_cadastro = ".$this->obRCadastroDinamico->getCodCadastro()." AND \n";
        $stFiltro .= "     cod_elemento = ".$this->inCodigoElemento;
        $obErro = $obTCEMAtributoElemCadEconomicoValor->recuperaTodos($rsRecordSet,$stFiltro,"",$boTransacao);
        if ( !$obErro->ocorreu() ) {
            if (!$rsRecordSet->eof()) {
                $obErro->setDescricao("Elemento ".$this->inCodigoElemento." - ".$this->getNomeElemento()." ainda está sendo referenciado pelo sistema!");
            } else {
                $this->obRCadastroDinamico->setPersistenteAtributos( new TCEMAtributoElemento() );
                $arChaveAtributo =  array( "cod_elemento" => $this->inCodigoElemento );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                $obErro = $this->obRCadastroDinamico->excluir( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTCEMElemento->setDado( "cod_elemento" , $this->inCodigoElemento );
                    $obErro = $this->obTCEMElemento->exclusao( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMElemento );

    return $obErro;
}

/**
    * Baixa o Elemento selecionado no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function baixarElemento($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMElementoAtividade.class.php" );
        $obTCEMElementoAtividade = new TCEMElementoAtividade;

        if ( $this->getCodigoElemento() ) {
            $stFiltro .= " AE.cod_elemento = ".$this->getCodigoElemento(). " AND ";
        }

        if ($stFiltro) {
            $stFiltro = "\r\n\t AND  ".substr($stFiltro,0,-4);
        }

        $obTCEMElementoAtividade->recuperaElementoAtividade ( $rsLista, $stFiltro, $stOrdem  );
        //$obTCEMElementoAtividade->debug();
        $registros = $rsLista->getNumLinhas();

        if ($registros < 1) {
            $this->obTCEMBaixaElemento->setDado( "cod_elemento" , $this->inCodigoElemento );
            $this->obTCEMBaixaElemento->setDado( "motivo"       , $this->stMotivo         );
            $obErro = $this->obTCEMBaixaElemento->inclusao( $boTransacao );
        } else {
            $obErro->setDescricao ('Elemento está sendo utilizado em uma Atividade Econômica! ('. $this->getCodigoElemento() . ' - '. $_REQUEST["stNomeElemento"] .')');
            //(cod_elemento - nome_elemento)');
        }

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMBaixaElemento );

    return $obErro;
}

/**
    * Recupera do abnco de dados os dados do TipoEdificacao selecionado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarElemento($boTransacao = "")
{
    $this->obTCEMElemento->setDado( "cod_elemento" , $this->inCodigoElemento );
    $obErro = $this->obTCEMElemento->recuperaPorChave( $rsElemento , $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stNomeElemento = $rsElemento->getCampo( "nom_elemento" );
//        $this->
    }

    return $obErro;
}

/**
    * Lista os Elementos segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarElemento(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoElemento) {
        $stFiltro .= " AND E.cod_elemento = ".$this->inCodigoElemento;
    }
    if ($this->stNomeElemento) {
        $stFiltro .= " AND UPPER (E.nom_elemento) like upper ('%".$this->stNomeElemento."%')";
    }
    $stOrdem = " ORDER BY E.nom_elemento ";

    $obErro = $this->obTCEMElemento->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Valida se o nome do elemento já foi cadastrado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function validaNomeElemento($boTransacao = "")
{
    $stFiltro = " WHERE ";
    if ($this->inCodigoElemento) {
        $stFiltro .= " cod_elemento <> ".$this->inCodigoElemento." AND";
    }
    $stFiltro .= " LOWER( nom_elemento ) ";
    $stFiltro .= " LIKE LOWER( '".$this->stNomeElemento."' ) ";
    $obErro = $this->obTCEMElemento->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $obErro->setDescricao( "Já existe um elemento cadastrado com o nome ".$this->stNomeElemento."!" );
    }

    return $obErro;
}

/**
    * Inclui dados setados na tabela de elemento_atividade
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function incluirAtividadeElemento($boFlagTransacao, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMElementoAtividade->setDado( "cod_atividade", $this->roCEMAtividade->getCodigoAtividade() );
        $this->obTCEMElementoAtividade->setDado( "cod_elemento",  $this->inCodigoElemento );
        $obErro = $this->obTCEMElementoAtividade->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMElementoAtividade );

    return $obErro;
}

/**
    * Inclui dados setados na tabela de elemento_atividade
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function incluirTipoLicencaDiversaElemento($boFlagTransacao, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMElementoTipoLicencaDiversa->setDado( "cod_tipo", $this->roRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa());

        $this->obTCEMElementoTipoLicencaDiversa->setDado( "cod_elemento",  $this->inCodigoElemento );
        $this->obTCEMElementoTipoLicencaDiversa->setDado( "ativo" , 't' );
        $obErro = $this->obTCEMElementoTipoLicencaDiversa->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMElementoTipoLicencaDiversa );

    return $obErro;
}
/**
    *Referencia um objeto a classe RCEMTipoLicencaDiversa
    *@acess Public
*/

function referenciaTipoLicencaDiversa(&$obRCEMTipoLicencaDiversa)
{
    $this->roRCEMTipoLicencaDiversa = &$obRCEMTipoLicencaDiversa;
}

/**
    * Lista os Elementos da Atividade segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarElementoAtividade(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->roCEMAtividade->getCodigoAtividade() ) {
        $stFiltro .= " AND A.COD_ATIVIDADE = ".$this->roCEMAtividade->getCodigoAtividade()." ";
    }

    if ($this->inCodigoElemento) {
        $stFiltro .= " AND EL.COD_ELEMENTO = ".$this->inCodigoElemento." ";
    }

    if ( $this->roCEMAtividade->getNomeAtividade() ) {
        $stFiltro .= " AND UPPER(A.NOM_ATIVIDADE) LIKE UPPER( '".$this->roCEMAtividade->getNomeAtividade()."%')";
    }

    if ($this->stNomeElemento) {
        $stFiltro .= " AND UPPER(EL.NOM_ELEMENTO) LIKE UPPER( '".$this->stNomeElemento."%')";
    }

    $stOrdem = " ORDER BY EL.COD_ELEMENTO ";
    $obErro = $this->obTCEMElementoAtividade->recuperaElementoAtividade( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}


/**
    * Lista os Elementos da Atividade selecionada
    * @access Public
    * @param  Object $rsRecordSet
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarElementoAtividadeSelecionados(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->roCEMAtividade->getCodigoAtividade() ) {
        $stFiltro .= " AND AE.COD_ATIVIDADE = ".$this->roCEMAtividade->getCodigoAtividade()." ";
    }

    if ($this->inCodigoElemento) {
        $stFiltro .= " AND EL.COD_ELEMENTO = ".$this->inCodigoElemento." ";
    }

    if ($this->stNomeElemento) {
        $stFiltro .= " AND UPPER(EL.NOM_ELEMENTO) LIKE UPPER( '".$this->stNomeElemento."%')";
    }

    $stOrdem = " ORDER BY EL.COD_ELEMENTO ";
    $obErro = $this->obTCEMElementoAtividade->recuperaElementoAtividadeSelecionados( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os Elementos de tipo de licença selecionada
    * @access Public
    * @param  Object $rsRecordSet
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarElementoTipoLicencaDiversaSelecionados(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa()) {
        $stFiltro .= " AND AE.COD_TIPO = ".$this->roRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa()." ";
    }

    if ($this->inCodigoElemento) {
        $stFiltro .= " AND EL.COD_ELEMENTO = ".$this->inCodigoElemento." ";
    }

    if ($this->stNomeElemento) {
        $stFiltro .= " AND UPPER(EL.NOM_ELEMENTO) LIKE UPPER( '".$this->stNomeElemento."%')";
    }

    $stOrdem = " ORDER BY EL.COD_ELEMENTO ";
    $obErro = $this->obTCEMElementoTipoLicencaDiversa->recuperaElementoTipoLicencaDiversaSelecionados( $rsRecordSet, $stFiltro,$stOrdem, $boTransacao );

        return $obErro;
}



/**
    * Lista os Elementos da Atividade disponível
    * @access Public
    * @param  Object $rsRecordSet
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarElementoAtividadeDisponiveis(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ($this->inCodigoElemento) {
        $stFiltro .= " AND EL.COD_ELEMENTO = ".$this->inCodigoElemento." ";
    }

    if ( $this->roCEMAtividade->getCodigoAtividade() ) {

        $stFiltro .= "
        WHERE
            EL.COD_ELEMENTO
        NOT IN
        (
            select EA.cod_elemento from economico.elemento_atividade as EA
            where EA.cod_atividade = ". $this->roCEMAtividade->getCodigoAtividade() ."
        ) ";
    }

    if ($this->stNomeElemento) {
        $stFiltro .= " AND UPPER(EL.NOM_ELEMENTO) LIKE UPPER( '".$this->stNomeElemento."%')";
    }

    $stOrdem = " ORDER BY EL.COD_ELEMENTO ";
    $obErro = $this->obTCEMElementoAtividade->recuperaElementoAtividadeDisponiveis( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    //$this->obTCEMElementoAtividade->debug();
    return $obErro;
}
/**
    * Lista os Elementos da Atividade disponível
    * @access Public
    * @param  Object $rsRecordSet
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarElementoTipoLicencaDiversaDisponiveis(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoElemento) {
        $stFiltro .= " AND EL.COD_ELEMENTO = ".$this->inCodigoElemento." ";
    }

    if ($this->stNomeElemento) {
        $stFiltro .= " AND UPPER(EL.NOM_ELEMENTO) LIKE UPPER( '".$this->stNomeElemento."%')";
    }

    if ( $this->roRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa()) {
        $stFiltro .= " EL.cod_elemento NOT IN ( SELECT  cod_elemento from economico.elemento_tipo_licenca_diversa WHERE cod_tipo = ".$this->roRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa()." )";
    }

    $stOrdem = " ORDER BY EL.COD_ELEMENTO ";
    $obErro = $this->obTCEMElementoTipoLicencaDiversa->recuperaElementoTipoLicencaDiversaDisponiveis( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}
/**
    * Lista os Elementos disponiveis para Tipo Licenca Diversa
    * @access Public
    * @param  Object $rsRecordSet
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarElementoTipoLicencaDiversa(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( is_object( $this->roRCEMTipoLicencaDiversa )) {
        if ( $this->roRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa() ) {
            $stFiltro .= " AND AE.COD_TIPO = ".$this->roRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa()." ";
        }
    }
    if ($this->inCodigoElemento) {
        $stFiltro .= " AND EL.COD_ELEMENTO = ".$this->inCodigoElemento." ";
    }

    $stOrdem = " ORDER BY EL.COD_ELEMENTO ";
    $obErro = $this->obTCEMElementoTipoLicencaDiversa->recuperaElementoTipoLicencaDiversa( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
//    $this->obTCEMElementoTipoLicencaDiversa->debug();
    return $obErro;
}

/**
    * Exclui dados setados na tabela de elemento_atividade
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function excluirElementoAtividade($boFlagTransacao, $boTransacao = "")
{
    $boFlagTransacao = false;
    $this->obTCEMElementoAtividade->setDado( "cod_atividade", $this->roCEMAtividade->getCodigoAtividade() );
    $this->obTCEMElementoAtividade->setDado( "cod_elemento",  $this->inCodigoElemento );
    $obErro = $this->obTCEMElementoAtividade->exclusao( $boTransacao );

    return $obErro;
}
/**
    * Exclui dados setados na tabela de elemento_atividade
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro Transação
    * @return Object Objeto Erro
*/
function excluirElementoTipoLicencaDiversa($boFlagTransacao, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMElementoTipoLicencaDiversa->setDado( "cod_tipo", $this->roCEMAtividade->getCodigoTipoLicencaDiversa() );
        $this->obTCEMElementoTipoLicencaDiversa->setDado( "cod_elemento",  $this->inCodigoElemento );
        $obErro = $this->obTCEMElementoTipoLicencaDiversa->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMElementoTipoLicencaDiversa );

    return $obErro;
}

}
?>
