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
* Classe de negócio CadastroDinamico
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Id: RCadastroDinamico.class.php 61695 2015-02-26 12:13:37Z franver $

$Revision: 20901 $
$Name$
$Author: cassiano $
$Date: 2007-03-12 11:32:23 -0300 (Seg, 12 Mar 2007) $

Casos de uso: uc-01.03.95
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_NEGOCIO."RCadastro.class.php"                  );
include_once( CAM_GA_ADM_NEGOCIO."RAtributoDinamico.class.php"        );
//NOVAS CLASSES DE MAPEAMENTO
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoCadastro.class.php" );
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php" );

/**
    * Classe de Regra de Negócio Cadastro Dinamico
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class RCadastroDinamico extends RCadastro
{
/**
    * @var Object
    * @access Private
*/
var $obPersistenteCadastro;
/**
    * @var Object
    * @access Private
*/
var $obPersistenteAtributos;
/**
    * @var Object
    * @access Private
*/
var $obPersistenteValores;
/**
    * @var Array
    * @access Private
*/
var $arAtributosDinamicos;
/**
    * @var Array
    * @access Private
*/
var $arChavePersistenteValores;

/**
    * @access Public
    * @param Object $valor
*/
function setPersistenteCadastro($valor) { $this->obPersistenteCadastro    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setPersistenteAtributos($valor) { $this->obPersistenteAtributos   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setPersistenteValores($valor) { $this->obPersistenteValores = $valor;     }
/**
    * @access Public
    * @param Array $valor
*/
function setAtributosDinamicos($valor) { $this->arAtributosDinamicos     = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setChavePersistenteValores($valor) { $this->arChavePersistenteValores= $valor; }

/**
    * @access Public
    * @return Object
*/
function getPersistenteCadastro() { return $this->obPersistenteCadastro;    }
/**
    * @access Public
    * @return Object
*/
function getPersistenteAtributos() { return $this->obPersistenteAtributos;   }
/**
    * @access Public
    * @return Object
*/
function getPersistenteValores() { return $this->obPersistenteValores;     }
/**
    * @access Public
    * @return Array
*/
function getAtributosDinamicos() { return $this->arAtributosDinamicos;     }
/**
    * @access Public
    * @return Array
*/
function getChavePersistenteValores() { return $this->arChavePersistenteValores;}

/**
    * Método Construtor
    * @access Private
*/
function RCadastroDinamico()
{
    parent::RCadastro                ( new RModulo );
    $this->setAtributosDinamicos     ( array() );
    $this->setChavePersistenteValores( array() );
    $this->verificaModulo            ();
    $this->setPersistenteCadastro  ( new TAdministracaoCadastro );
    $this->setPersistenteAtributos ( new TAdministracaoAtributoDinamico );
}

/**
    * Método para adicionar Objetos da classe RAtributoDinamico
    * @access Public
    * @param Integer $inCodAtributo
*/
function addAtributosDinamicos($inCodAtributo , $valor = "")
{
    $obAtributoDinamico = new RAtributoDinamico;
    $obAtributoDinamico->setCodAtributo( $inCodAtributo );
    $obAtributoDinamico->setValor      ( $valor );
    array_push( $this->arAtributosDinamicos, $obAtributoDinamico );
}

/**
    * Parte de verificação do módulo a ser utilizado.
    * Conforme a necessidade, e a existência de outros módulos, deverão ser adicionados aqui conforme exemplo dos já existentes.
    * A classe PersistenteAtributos deverá ser uma extensão da PersistenteAtributos.
    * @access Public
*/
function verificaModulo()
{
    $inCodModulo = Sessao::read('modulo')?Sessao::read('modulo'):$_REQUEST['modulo'];
    $this->obRModulo->setCodModulo( ($this->obRModulo->getCodModulo())?$this->obRModulo->getCodModulo():$inCodModulo );
    Sessao::write('modulo', $inCodModulo);
}

function verificaAtributoValor($boTransacao = "")
{
    $obErro = new Erro;
    if ( $this->getPersistenteValores() == '' ) {
        $this->verificaModulo();
        $this->obPersistenteCadastro->setDado( "cod_cadastro", $this->getCodCadastro() );
        $obErro = $this->obPersistenteCadastro->recuperaPorChave( $rsPersistenteCadastro, $boTransacao );
        $stFiltro  = " AND m.cod_modulo = ".$this->obRModulo->getCodModulo()." \n";
        $stFiltro .= " AND c.cod_cadastro = ".$this->getCodCadastro();
        $obErro = $this->obPersistenteCadastro->recuperaRelacionamento($rsPersistenteCadastro,$stFiltro,'',$boTransacao );
        if ( !$obErro->ocorreu() ) {
            $stCaminho  = $rsPersistenteCadastro->getCampo( 'nom_diretorio_gestao' );
            $stCaminho .= $rsPersistenteCadastro->getCampo( 'nom_diretorio_modulo' );
            $stCaminho .= 'classes/mapeamento/';
            $stCaminho .= $rsPersistenteCadastro->getCampo('mapeamento').".class.php";
            include_once( $stCaminho );
            $stClasseMapeamento = $rsPersistenteCadastro->getCampo('mapeamento');
            $this->setPersistenteValores( new  $stClasseMapeamento );
        }
    }

    return $obErro;
}

/**
    * Executa Inclusão/Alteração dos Atributos do Cadastro Dinamico
    * @access Public
    * @return Object Objeto Erro
*/
function salvar($boTransacao="")
{
    $obErro = new Erro;
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //BUSCA OS ATRIBUTOS ATIVOS DO MODULO E CADASTRO SELECIONADO
        $stFiltro  = " WHERE cod_cadastro=".$this->getCodCadastro();
        $stFiltro .= " AND cod_modulo = ".$this->obRModulo->getCodModulo();
        $stFiltro .= " AND ativo = true ";
        //Verifica a tipagem do campo, e monta a string de filtro
        foreach ($this->getChavePersistenteValores() as $key=>$value) {
            foreach ($this->obPersistenteAtributos->GetEstrutura() as $obCampo) {
                if ( $obCampo->GetNomeCampo() == $key ) {
                    $stFiltro .= " AND $key = ";
                    switch ($obCampo->GetTipoCampo()) {
                        case("text"):
                        case("timestamp"):
                        case("varchar"):
                            $stFiltro .= "   '".$value."' ";
                        break;
                        case("numeric"):
                            $nrValor = str_replace('.', '', $value );
                            $nrValor = str_replace(',', '.', $nrValor );
                            $stFiltro .= "  '".$nrValor."' ";
                        break;
                        default:
                            $stFiltro .= "   ".$value." ";
                        break;
                    }
                }
            }
        }

        $obErro = $this->obPersistenteAtributos->recuperaTodos( $rsPersistenteAtributos, $stFiltro , '', $boTransacao);
        if ( !$obErro->ocorreu() ) {
            //DESATIVA OS ATRIBUTOS DO CADASTRO SELECIONADO
            while ( !$rsPersistenteAtributos->eof() ) {
                $this->obPersistenteAtributos->setDado( 'cod_modulo'  , $this->obRModulo->getCodModulo()  );
                $this->obPersistenteAtributos->setDado( 'cod_cadastro', $this->getCodCadastro() );
                $this->obPersistenteAtributos->setDado( 'cod_atributo', $rsPersistenteAtributos->getCampo('cod_atributo') );
                $this->obPersistenteAtributos->setDado( 'ativo'       , 'false' );

                //Fase de testes
                foreach ($this->getChavePersistenteValores() as $key=>$value) {
                    foreach ($this->obPersistenteAtributos->GetEstrutura() as $obCampo) {
                        if ( $obCampo->GetNomeCampo() == $key ) {
                            $this->obPersistenteAtributos->setDado( $key , $value );
                        }
                    }
                }
                $obErro = $this->obPersistenteAtributos->alteracao( $boTransacao );
                if( $obErro->ocorreu() )

                    return $obErro;
                elseif ( $rsPersistenteAtributos->getCampo('ativo') != "f" ) {
                    $obAtribrutoDinamico = new RAtributoDinamico;
                    $obAtribrutoDinamico->setCodAtributo($rsPersistenteAtributos->getCampo('cod_atributo') );
                }

                $rsPersistenteAtributos->proximo();
            }
            //ATIVA OS ATRIBUTOS SETADOS
            foreach ( $this->getAtributosDinamicos() as $obAtributoDinamico ) {
                $this->obPersistenteAtributos->setDado( 'cod_cadastro', $this->getCodCadastro() );
                $this->obPersistenteAtributos->setDado( 'cod_modulo'  , $this->obRModulo->getCodModulo() );
                $this->obPersistenteAtributos->setDado( 'cod_atributo', $obAtributoDinamico->getCodAtributo() );
                $this->obPersistenteAtributos->setDado( 'ativo'       , 'true' );

                //Fase de testes
                foreach ($this->getChavePersistenteValores() as $key=>$value) {
                    foreach ($this->obPersistenteAtributos->GetEstrutura() as $obCampo) {
                        if ( $obCampo->GetNomeCampo() == $key ) {
                            $this->obPersistenteAtributos->setDado( $key , $value );
                        }
                    }
                }
                //VERIFICA SE O ATRIBUTO JÁ EXISTE NO BANCO PARA DETERMINAR SE É UMA INCLUSAO OU ALTERACAO
                $obErro = $this->obPersistenteAtributos->recuperaPorChave( $rsPersistenteAtributos, $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    if ( $rsPersistenteAtributos->eof() ) {
                        $obErro = $this->obPersistenteAtributos->inclusao( $boTransacao );
                    } else {
                        $obErro = $this->obPersistenteAtributos->alteracao( $boTransacao );
                    }
                }
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Executa Exclusão dos Atributos do Cadastro Dinamico
    * @access Public
    * @return Object Objeto Erro
*/
function excluir($boTransacao="")
{
    $obErro = new Erro;
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //Armazena os valores existentes das chaves
        $stCampoCod         = $this->obPersistenteAtributos->getCampoCod();
        $stComplementoChave = $this->obPersistenteAtributos->getComplementoChave();
        $this->obPersistenteAtributos->setDado( 'cod_cadastro', $this->getCodCadastro() );
        $stComplementoChaveTmp = 'cod_cadastro';
        //Fase de testes
        foreach ($this->getChavePersistenteValores() as $key=>$value) {
            foreach ($this->obPersistenteAtributos->GetEstrutura() as $obCampo) {
                if ( $obCampo->GetNomeCampo() == $key ) {
                    $this->obPersistenteAtributos->setDado( $key , $value );
                    $stComplementoChaveTmp .= ",$key";
                }
            }
        }
        $this->obPersistenteAtributos->setComplementoChave( $stComplementoChaveTmp );
        $obErro = $this->obPersistenteAtributos->exclusao( $boTransacao );
        //Retorna com os valores padrões
        $this->obPersistenteAtributos->setCampoCod( $stCampoCod );
        $this->obPersistenteAtributos->setComplementoChave( $stComplementoChave );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Efetua um recuperaTodos na classe de mapeamento setada a partir do método verificaModulo
    * @access Public
    * @param  Object $rsRecordSet Objeto de saida do tipo RecordSet
    * @param  String $stOrder
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function listar(&$rsRecordSet, $stFiltro = "", $stOrder="" ,$boTransacao = "")
{
    $this->verificaModulo();
    $obErro = $this->obPersistenteCadastro->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaAtributos na classe Persistente Atributos setada no método verificador de módulo.
    * A classe PersistenteAtributos deverá ser uma extensão da PersistenteAtributos.
    * @access Public
    * @param  Object $rsRecordSet Objeto de saida do tipo RecordSet
    * @param  String $stOrder
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function recuperaAtributos(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $this->obPersistenteAtributos->setDado( 'cod_cadastro', $this->getCodCadastro() );
    $this->obPersistenteAtributos->setDado( 'cod_modulo'  , $this->obRModulo->getCodModulo() );

    foreach ($this->getChavePersistenteValores() as $key=>$value) {
        foreach ($this->obPersistenteAtributos->GetEstrutura() as $obCampo) {
            if ( $obCampo->GetNomeCampo() == $key ) {
                $this->obPersistenteAtributos->setDado( $key , $value );
            }
        }
    }

    $obErro = $this->obPersistenteAtributos->recuperaAtributos( $rsRecordSet, '', '', $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaAtributosDisponiveis na classe Persistente Atributos setada no método verificador de módulo.
    * A classe PersistenteAtributos deverá ser uma extensão da PersistenteAtributos.
    * @access Public
    * @param  Object $rsRecordSet Objeto de saida do tipo RecordSet
    * @param  String $stOrder
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function recuperaAtributosDisponiveis(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $this->obPersistenteAtributos->setDado( 'cod_cadastro', $this->getCodCadastro() );
    $this->obPersistenteAtributos->setDado( 'cod_modulo'  , $this->obRModulo->getCodModulo() );

    $stFiltro = "";
    foreach ($this->getChavePersistenteValores() as $key=>$value) {
        foreach ($this->obPersistenteAtributos->GetEstrutura() as $obCampo) {
            if ( $obCampo->GetNomeCampo() == $key ) {
                $this->obPersistenteAtributos->setDado( $key , $value );

                switch ($obCampo->GetTipoCampo()) {
                    case("text"):
                    case("timestamp"):
                    case("varchar"):
                        $stFiltro .= " AND ".$key." = '".$value."'";
                    break;
                    case("numeric"):
                        $nrValor = str_replace('.', '', $value );
                        $nrValor = str_replace(',', '.', $nrValor );
                        $stFiltro .= " AND ".$key." = ".$value;
                    break;
                    default:
                        $stFiltro .= " AND ".$key." = ".$value;
                    break;
                }
            }
        }
    }

    if ($stFiltro) {
        $this->obPersistenteAtributos->setDado( "stFiltro" , $stFiltro );
    }
    $obErro = $this->obPersistenteAtributos->recuperaAtributosDisponiveis( $rsRecordSet, '', '', $boTransacao );

    return $obErro;
}
/**
    * Executa um recuperaAtributosSelecionados na classe Persistente Atributos setada no método verificador de módulo.
    * A classe PersistenteAtributos deverá ser uma extensão da PersistenteAtributos.
    * @access Public
    * @param  Object $rsRecordSet Objeto de saida do tipo RecordSet
    * @param  String $stOrder
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function recuperaAtributosSelecionados(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $this->obPersistenteAtributos->setDado( 'cod_cadastro', $this->getCodCadastro() );
    $this->obPersistenteAtributos->setDado( 'cod_modulo'  , $this->obRModulo->getCodModulo() );
    $stFiltro = "";
    foreach ($this->getChavePersistenteValores() as $key=>$value) {
        foreach ($this->obPersistenteAtributos->GetEstrutura() as $obCampo) {
            if ( $obCampo->GetNomeCampo() == $key ) {
                //>05-08-2004
                $stFiltro   .= " AND ACA.$key = ";
                switch ($obCampo->GetTipoCampo()) {
                    case("text"):
                    case("timestamp"):
                    case("varchar"):
                        $stFiltro  .= "   '".$value."' ";
                    break;
                    case("numeric"):
                        $nrValor = str_replace('.', '', $value );
                        $nrValor = str_replace(',', '.', $nrValor );
                        $stFiltro  .= "  '".$nrValor."' ";
                    break;
                    default:
                        $stFiltro  .= "   ".$value." ";
                    break;
                }
                //<05-08-2004
                $this->obPersistenteAtributos->setDado( $key , $value );
            }
        }
    }

    $obErro = $this->obPersistenteAtributos->recuperaAtributosSelecionados( $rsRecordSet, $stFiltro, '', $boTransacao );

    return $obErro;
}
/**
    * Executa um recuperaTodos na classe de mapeamento setada a partir do método verificaModulo
    * @access Public
    * @param  Object $rsRecordSet Objeto de saida do tipo RecordSet
    * @param  String $stOrder
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function recuperaAtributosSelecionadosValores(&$rsRecordSet, $stFiltro="" ,$stOrder="" ,$boTransacao = "")
{
    return $this->_recuperaAtributosSelecionadosValores( true, $rsRecordSet, $stFiltro ,$stOrder ,$boTransacao);
}

/**
    * Recupera o valor dos atributos ativos e inativos
    * @access Public
    * @param  Object $rsRecordSet Objeto de saida do tipo RecordSet
    * @param  String $stOrder
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function consultaAtributosSelecionadosValores(&$rsRecordSet, $stFiltro="" ,$stOrder="" ,$boTransacao = "")
{
    return $this->_recuperaAtributosSelecionadosValores( false, $rsRecordSet, $stFiltro ,$stOrder ,$boTransacao);
}

function _recuperaAtributosSelecionadosValores($boAtivos, &$rsRecordSet, $stFiltro="" ,$stOrder="" ,$boTransacao)
{
    $stFiltroValores = $stGroup = $stCondicao = $stFiltroAtributos = "";

    $obErro = $this->verificaAtributoValor( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //Verifica a tipagem do campo, e monta a string de filtro
        foreach ($this->getChavePersistenteValores() as $key=>$value) {
            //MONTA O FILTRO DA TABELA DE VALORES
            foreach ($this->obPersistenteValores->GetEstrutura() as $obCampo) {
                if ( $obCampo->GetNomeCampo() == $key ) {
                    $stFiltroValores   .= " AND VALOR.$key = ";

                        //MONTA O FILTRO DA TABELA RELACIONADA A DE VALORES
                        foreach ($this->obPersistenteValores->obPersistenteAtributo->GetEstrutura() as $obCampoAtr) {
                            if ( $obCampoAtr->GetNomeCampo() == $key ) {
                                $stCondicao        .= " AND VALOR.$key = ACA.$key ";
                                $stFiltroAtributos .= " AND ACA.$key = ";

                                switch ($obCampoAtr->GetTipoCampo()) {
                                    case("text"):
                                    case("timestamp"):
                                    case("varchar"):
                                        $stFiltroAtributos  .= "   '".$value."' ";
                                    break;
                                    case("numeric"):
                                        $nrValor = str_replace('.', '', $value );
                                        $nrValor = str_replace(',', '.', $nrValor );
                                        $stFiltroAtributos  .= "  '".$nrValor."' ";
                                    break;
                                    case("char"):
                                        $stFiltroAtributos  .= "   '".$value."' ";
                                    break;
                                    default:
                                        $stFiltroAtributos  .= "   ".$value." ";
                                    break;
                                }
                                break;
                            }
                        }
                    $stGroup  .= " ,VALOR.$key ";

                    switch ($obCampo->GetTipoCampo()) {
                        case("text"):
                        case("timestamp"):
                        case("varchar"):
                            $stFiltroValores  .= "   '".$value."' ";
                        break;
                        case("numeric"):
                            $nrValor = str_replace('.', '', $value );
                            $nrValor = str_replace(',', '.', $nrValor );
                            $stFiltroValores  .= "  '".$nrValor."' ";
                        break;
                        case("char"):
                            $stFiltroValores  .= "   '".$value."' ";
                        break;
                        default:
                            $stFiltroValores  .= "   ".$value." ";
                        break;
                    }
                }
            }
        }

        foreach ($this->getChavePersistenteValores() as $key=>$value) {
            foreach ($this->obPersistenteValores->GetEstrutura() as $obCampo) {
                if ( $obCampo->GetNomeCampo() == $key ) {
                    $this->obPersistenteValores->setDado( $key , $value );
                }
            }
        }

        $this->obPersistenteValores->setDado( 'cod_cadastro', $this->getCodCadastro() );
        $this->obPersistenteValores->setDado( 'cod_modulo'  , $this->obRModulo->getCodModulo() );
        $this->obPersistenteValores->setDado( 'stFiltroValores'    , $stFiltroValores );
        $this->obPersistenteValores->setDado( 'stFiltroAtributos'  , $stFiltroAtributos );
        $this->obPersistenteValores->setDado( 'stGroupBy'   , $stGroup );
        $this->obPersistenteValores->setDado( 'stCondicao'  , $stCondicao );

        if ($boAtivos) {
            $obErro = $this->obPersistenteValores->recuperaAtributosSelecionadosValores( $rsRecordSet, '', $stOrder, $boTransacao );
        } else {
            $obErro = $this->obPersistenteValores->recuperaAtributosAtivosInativosValores( $rsRecordSet, '', $stOrder, $boTransacao );
        }
    }

    return $obErro;
}

/**
    * Efetua um recuperaTodos na classe de mapeamento setada a partir do método verificaModulo
    * @access Public
    * @param  Object $rsRecordSet Objeto de saida do tipo RecordSet
    * @param  String $stOrder
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function recuperaAtributosSelecionadosValoresHistorico(&$rsRecordSet, $stFiltro="" ,$stOrder="" ,$boTransacao = "")
{
    $stFiltroValores = $stGroup = $stFiltroAtributos = $stCondicao = "";
    //Verifica a tipagem do campo, e monta a string de filtro
    foreach ($this->getChavePersistenteValores() as $key=>$value) {
        //MONTA O FILTRO DA TABELA DE VALORES
        foreach ($this->obPersistenteValores->GetEstrutura() as $obCampo) {
            if ( $obCampo->GetNomeCampo() == $key ) {
                $stFiltroValores   .= " AND VALOR.$key = ";
                    //MONTA O FILTRO DA TABELA RELACIONADA A DE VALORES
                    foreach ($this->obPersistenteValores->obPersistenteAtributo->GetEstrutura() as $obCampoAtr) {
                        if ( $obCampoAtr->GetNomeCampo() == $key ) {
                            $stCondicao        .= " AND VALOR.$key = ACA.$key ";
                            $stFiltroAtributos .= " AND ACA.$key = ";
                            switch ($obCampoAtr->GetTipoCampo()) {
                                case("text"):
                                case("timestamp"):
                                case("varchar"):
                                    $stFiltroAtributos  .= "   '".$value."' ";
                                break;
                                case("numeric"):
                                    $nrValor = str_replace('.', '', $value );
                                    $nrValor = str_replace(',', '.', $nrValor );
                                    $stFiltroAtributos  .= "  '".$nrValor."' ";
                                break;
                                default:
                                    $stFiltroAtributos  .= "   ".$value." ";
                                break;
                       }
                            break;
                        }
                    }
                $stGroup  .= " ,VALOR.$key ";
                switch ($obCampo->GetTipoCampo()) {
                    case("text"):
                    case("varchar"):
                        $stFiltroValores  .= "   '".$value."' ";
                    break;
                    case("numeric"):
                        $nrValor = str_replace('.', '', $value );
                        $nrValor = str_replace(',', '.', $nrValor );
                        $stFiltroValores  .= "  '".$nrValor."' ";
                    break;
                    default:
                        $stFiltroValores  .= "   ".$value." ";
                    break;
                }
            }
        }
    }

    foreach ($this->getChavePersistenteValores() as $key=>$value) {
        foreach ($this->obPersistenteValores->GetEstrutura() as $obCampo) {
            if ( $obCampo->GetNomeCampo() == $key ) {
                $this->obPersistenteValores->setDado( $key , $value );
            }
        }
    }

    $this->obPersistenteValores->setDado( 'cod_cadastro', $this->getCodCadastro() );
    $this->obPersistenteValores->setDado( 'cod_modulo'  , $this->obRModulo->getCodModulo() );
    $this->obPersistenteValores->setDado( 'stFiltroValores'    , $stFiltroValores );
    $this->obPersistenteValores->setDado( 'stFiltroAtributos'  , $stFiltroAtributos );
    $this->obPersistenteValores->setDado( 'stGroupBy'   , $stGroup );
    $this->obPersistenteValores->setDado( 'stCondicao'  , $stCondicao );
    $obErro = $this->obPersistenteValores->recuperaAtributosSelecionadosValoresHistorico( $rsRecordSet, '', $stOrder, $boTransacao);

    return $obErro;
}
/**
    * Salvar
    * @access Public
    * @return Object Objeto erro
*/
function salvarValores($boTransacao = "")
{
    $this->verificaModulo();
    $obErro = new Erro;
    $this->verificaModulo();

    if( empty($stFiltro) )
        $stFiltro = "";

    $obErro = $this->verificaAtributoValor( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stOrder  = " ORDER BY VALOR.timestamp DESC ";
        $obErro = $this->recuperaAtributosSelecionadosValores($rsAtributosSelecionadosValores, $stFiltro, $stOrder, $boTransacao);

        if ( !$obErro->ocorreu() ) {
            $arValorAtributo = array();
            while ( !$rsAtributosSelecionadosValores->eof() ) {
                 $arValorAtributo[$rsAtributosSelecionadosValores->getCampo("cod_atributo")] = $rsAtributosSelecionadosValores->getCampo("valor");
                 $rsAtributosSelecionadosValores->proximo();
            }
            foreach ( $this->getAtributosDinamicos() as $obAtributoDinamico ) {

                if (array_key_exists($obAtributoDinamico->getCodAtributo(), $arValorAtributo)) {

                    if ( $arValorAtributo[$obAtributoDinamico->getCodAtributo()]."_" != $obAtributoDinamico->getValor()."_" ) {
                        $this->obPersistenteValores->setDado("cod_modulo" ,   $this->obRModulo->getCodModulo()      );
                        $this->obPersistenteValores->setDado("cod_atributo" , $obAtributoDinamico->getCodAtributo() );
                        $this->obPersistenteValores->setDado("cod_cadastro" , $this->getCodCadastro() );
                        $this->obPersistenteValores->setDado("valor"        , $obAtributoDinamico->getValor() );
                        foreach ($this->getChavePersistenteValores() as $key=>$value) {
                            $this->obPersistenteValores->setDado($key , $value );
                        }
                        $obErro = $this->obPersistenteValores->inclusao( $boTransacao );
                        if( $obErro->ocorreu() )

                            return $obErro;
                    }
                }
            }
        }
    }

    return $obErro;
}

//salva dados dinamicos sem validar se estes ja foram gravados na tabela (gerando um historico pelo timestamp)
function salvarValoresTimestamp($boTransacao = "")
{
    $this->verificaModulo();
    $obErro = new Erro;
    $this->verificaModulo();
    $obErro = $this->verificaAtributoValor( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        foreach ( $this->getAtributosDinamicos() as $obAtributoDinamico ) {
            $this->obPersistenteValores->setDado("cod_modulo" ,   $this->obRModulo->getCodModulo()      );
            $this->obPersistenteValores->setDado("cod_atributo" , $obAtributoDinamico->getCodAtributo() );
            $this->obPersistenteValores->setDado("cod_cadastro" , $this->getCodCadastro() );
            $this->obPersistenteValores->setDado("valor"        , $obAtributoDinamico->getValor() );
            foreach ($this->getChavePersistenteValores() as $key=>$value) {
                $this->obPersistenteValores->setDado($key , $value );
            }
            $obErro = $this->obPersistenteValores->inclusao( $boTransacao );
            if( $obErro->ocorreu() )

                return $obErro;
        }
    }

    return $obErro;
}

/**
    * Alterar
    * @access Public
    * @return Object Objeto erro
*/
function alterarValores($boTransacao = "")
{
    $this->verificaModulo();
    $obErro = $this->verificaAtributoValor( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        foreach ( $this->getAtributosDinamicos() as $obAtributoDinamico ) {
            $arAtributoDinamico = explode('-', $obAtributoDinamico->getCodAtributo());
            $stFiltro = " AND ACA.cod_atributo = ".$arAtributoDinamico[0];
            $stOrder  = " ORDER BY VALOR.timestamp DESC ";
            $this->obPersistenteValores->setDado('stFiltro',$stFiltro);
            $obErro = $this->recuperaAtributosSelecionadosValores($rsAtributosSelecionadosValores, $stFiltro, $stOrder, $boTransacao);
            if ( ( $rsAtributosSelecionadosValores->getCampo("valor") != $obAtributoDinamico->getValor() )&&( !$obErro->ocorreu() ) ) {
                $this->obPersistenteValores->setDado("cod_atributo" , $arAtributoDinamico[0]);
                $this->obPersistenteValores->setDado("cod_cadastro" , $this->getCodCadastro() );
                $this->obPersistenteValores->setDado("valor"        , $obAtributoDinamico->getValor() );
                $this->obPersistenteValores->setDado("timestamp"    , $rsAtributosSelecionadosValores->getCampo("timestamp") );
                foreach ($this->getChavePersistenteValores() as $key=>$value) {
                    $this->obPersistenteValores->setDado($key , $value );
                }
                if ( !$rsAtributosSelecionadosValores->getCampo("timestamp") ) {
                    $obErro = $this->obPersistenteValores->inclusao( $boTransacao );
                } else {
                    $stComplementoChave = $this->obPersistenteValores->getComplementoChave();
                    $this->obPersistenteValores->setComplementoChave( $stComplementoChave.",timestamp" );
                    $obErro = $this->obPersistenteValores->alteracao( $boTransacao );
                    $this->obPersistenteValores->setComplementoChave( $stComplementoChave );
                }
                if ( $obErro->ocorreu() ) {
                    return $obErro;
                }
            }
        }
    }

    return $obErro;
}

/**
    * Excluir Valores
    * @access Public
    * @return Object Objeto erro
*/
function excluirValores($boTransacao = "")
{
    $this->verificaModulo();
    $obErro = new Erro;
    $obErro = $this->verificaAtributoValor( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obPersistenteValores->setDado("cod_modulo" , $this->obRModulo->getCodModulo() );
        $this->obPersistenteValores->setDado("cod_cadastro" , $this->getCodCadastro() );
        $stComplementoChaveExclusao = "cod_cadastro,cod_modulo,";
        foreach ($this->getChavePersistenteValores() as $key=>$value) {
            $this->obPersistenteValores->setDado($key , $value );
            $stComplementoChaveExclusao .= $key.",";
        }
        $stComplementoChaveExclusao = substr(  $stComplementoChaveExclusao, 0, strlen( $stComplementoChaveExclusao ) - 1 );
        $stComplementoChave = $this->obPersistenteValores->getComplementoChave();
        $this->obPersistenteValores->setComplementoChave ( $stComplementoChaveExclusao );
        $obErro = $this->obPersistenteValores->exclusao  ( $boTransacao        );
        $this->obPersistenteValores->setComplementoChave ( $stComplementoChave );
    }

    return $obErro;
}

//MÉTODOS NOVOS EM FUNÇÃO DA ALTERAÇÃO DOS SCHEMAS DE BANCO
function recuperaCadastros(&$rsCadastro,$boTransacao = "")
{
    $stFiltro = "";
    if ( $this->obRModulo->getCodModulo() ) {
        $stFiltro .= " cod_modulo = ".$this->obRModulo->getCodModulo()." AND ";
    }
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    $stOrdem = " ORDER BY COD_MODULO, COD_CADASTRO ";
    $obErro = $this->obPersistenteCadastro->recuperaTodos( $rsCadastro, $stFiltro,$stOrdem,$boTransacao );

    return $obErro;
}

//MÉTODOS PARA O GERADOR DE CALCULOS
function geraFuncaoPL($obAtribrutoDinamico, $boTransacao)
{
    $this->verificaModulo();
    $stFiltro  = " AND m.cod_modulo = ".$this->obRModulo->getCodModulo()." \n";
    $stFiltro .= " AND c.cod_cadastro = ".$this->getCodCadastro();
    $obErro = $this->obPersistenteCadastro->recuperaRelacionamento( $rsPersistenteCadastro, $stFiltro, '', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stCaminho  = $rsPersistenteCadastro->getCampo( 'nom_diretorio_gestao' );
        $stCaminho .= $rsPersistenteCadastro->getCampo( 'nom_diretorio_modulo' );
        $stCaminho .= 'classes/mapeamento/';
        $stCaminho .= $rsPersistenteCadastro->getCampo('mapeamento').".class.php";
        include_once( $stCaminho );
        $stClasseMapeamento = $rsPersistenteCadastro->getCampo('mapeamento');

        $obPersistenteAtributoValor = new $stClasseMapeamento;

        $obPersistenteAtributoValor->setDado("cod_modulo", $this->obRModulo->getCodModulo() );
        $obPersistenteAtributoValor->setDado("cod_cadastro", $this->getCodCadastro() );

        $arChaveAtributoValor = explode( ",", $obPersistenteAtributoValor->getComplementoChave() );

        $stChaveAtributo = "";
        $arParametros = array();
        foreach ($arChaveAtributoValor as $inIndice => $stCampo) {
            if ($stCampo != "timestamp" AND $stCampo != "cod_cadastro" AND $stCampo != "cod_atributo") {
                $stParametro    = "in".str_replace( " ","", ucwords( str_replace( "_"," ", $stCampo ) ) );
                $arParametros[] = $stParametro;
                $stChaveAtributo .= " AND VALOR.".$stCampo." = \'\'||".$stParametro."||\'\' ";
            }
        }

        $obPersistenteAtributoValor->setDado("stCondicao", $stChaveAtributo );

        $obErro = $obAtribrutoDinamico->consultar( $rsAtributo, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obRModulo->consultar( $rsModulo, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arNomeFuncao["nom_modulo"]   = ucwords( $this->obRModulo->getNomModulo() );
                $arNomeFuncao["nom_cadastro"] = ucwords( $rsPersistenteCadastro->getCampo('nom_cadastro') );
                $arNomeFuncao["nom_atributo"] = ucwords( $obAtribrutoDinamico->getNome() );
                $stNomeFuncao = "recupera";
                $ar1 = array('/ /','/á/','/à/','/â/','/ã/','/é/','/è/','/ê/','/í/','/ì/',
                             '/î/','/ó/','/ò/','/õ/','/ô/','/ú/','/ù/','/û/','/ü/','/Á/','/À/','/Â/',
                             '/Ã/','/É/','/È/','/Ê/','/Í/','/Ì/','/Î/','/Ó/','/Ò/','/Õ/','/Ô/','/Ú/',
                             '/Ù/','/Û/','/Ü/','/ç/','/Ç/');

                $ar2 = array('','a','a','a','a','e','e','e','i','i','i','o','o','o','o','u','u','u',
                             'u','A','A','A','A','E','E','E','I','I','I','O','O','O','O','U','U','U',
                             'U','c','C');

                foreach ($arNomeFuncao as $stNome) {
                    $stNome = preg_replace( $ar1, $ar2, $stNome );
                    $stNomeFuncao .= $stNome;
                }
                $stSql = $stNomeFuncao."( ";
                $stParametros = "DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;";
                foreach ($arParametros as $stNomeParametro) {
                     $stSql .= " INTEGER,";
                     $stParametros .= $stNomeParametro." ALIAS FOR $".++$i.";";
                }
                $stSql = substr( $stSql, 0, strlen( $stSql ) - 1 )." )";
                $stSql = "CREATE OR REPLACE FUNCTION ".$stSql." RETURNS VARCHAR AS '";
                $stSql .= $stParametros;
                $stSql .= "BEGIN";
                $stSql .= " stSql := ''".str_replace( "''", "''''''''",

$obPersistenteAtributoValor->montaRecuperaAtributosSelecionadosValores()).
" AND ACA.cod_atributo = ".$obAtribrutoDinamico->getCodAtributo().";'';";

                $stSql = str_replace( "\'\'||", "''||", $stSql );
                $stSql = str_replace( "||\'\'", "||''", $stSql );
                $stSql .=  "OPEN crCursor FOR EXECUTE stSql;
                            FETCH crCursor INTO rsRetorno;
                            CLOSE crCursor;
                            RETURN rsRetorno.valor;
                            END;
                            ' LANGUAGE plpgsql;";
                $obErro = $this->verifcaBibliotecaFuncaoPL( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTGCFuncao = new TGCFuncao;
                    $obErro = $this->obTGCFuncao->proximoCod( $inCodFuncao, $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->obTGCFuncao->setDado( "cod_funcao", $inCodFuncao );
                        $this->obTGCFuncao->setDado( "cod_tipo_retorno", 2 );
                        $this->obTGCFuncao->setDado( "nom_funcao", $stNomeFuncao );
                        $obErro = $this->obTGCFuncao->inclusao( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $this->obTGCFuncaoInterna = new TGCFuncaoInterna;
                            $this->obTGCFuncaoInterna->setDado( "cod_funcao", $inCodFuncao );
                            $this->obTGCFuncaoInterna->setDado( "cod_biblioteca", $this->obTGCModuloBiblioteca->getDado( "cod_biblioteca" ) );
                            $obErro = $this->obTGCFuncaoInterna->inclusao( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                foreach ($arParametros as $stNomeParametro) {
                                    $this->obTGCVariavel = new TGCVariavel;
                                    $this->obTGCVariavel->setDado( "cod_funcao", $inCodFuncao );
                                    $this->obTGCVariavel->setDado( "cod_tipo", 1 );
                                    $this->obTGCVariavel->setDado( "nom_variavel", $stNomeParametro );
                                    $obErro = $this->obTGCVariavel->proximoCod( $inCodVariavel, $boTransacao );
                                    if ( !$obErro->ocorreu() ) {
                                        $this->obTGCVariavel->setDado( "cod_variavel", $inCodVariavel );
                                        $this->obTGCVariavel->setDado( "valor_inicial", "" );
                                        $obErro = $this->obTGCVariavel->inclusao( $boTransacao );
                                        if ( !$obErro->ocorreu() ) {
                                            $this->obTGCParametro = new TGCParametro;
                                            $this->obTGCParametro->setDado( "cod_variavel", $inCodVariavel );
                                            $this->obTGCParametro->setDado( "cod_funcao", $inCodFuncao );
                                            $this->obTGCParametro->setDado( "ordem", ++$inOrdemParametro );
                                            $obErro = $this->obTGCParametro->inclusao( $boTransacao );
                                            if ( !$obErro->ocorreu() ) {
                                                break;
                                            }
                                        } else {
                                            break;
                                        }
                                    } else {
                                        break;
                                    }
                                }
                                if ( !$obErro->ocorreu() ) {
                                    $obConexao  = new Conexao;
                                    $obErro = $obConexao->executaDML( $stSql, $boTransacao );
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return $obErro;
}

function verifcaBibliotecaFuncaoPL($boTransacao="")
{
    $this->obTGCModuloBiblioteca = new TGCModuloBiblioteca;
    $stFiltro = " WHERE COD_MODULO = ".$this->obRModulo->getCodModulo();
    $obErro = $this->obTGCModuloBiblioteca->recuperaTodos( $rsModuloBiblioteca, $stFiltro, "", $boTransacao );
    if ( !$obErro->ocorreu() and $rsModuloBiblioteca->eof() ) {
        $this->obTGCBiblioteca =  new TGCBiblioteca;
        $obErro = $this->obTGCBiblioteca->proximoCod( $inCodBiblioteca, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTGCBiblioteca->setDado( "cod_biblioteca", $inCodBiblioteca );
            $this->obTGCBiblioteca->setDado( "nom_biblioteca", $this->obRModulo->getNomModulo() );
            $obErro =  $this->obTGCBiblioteca->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                 $this->obTGCModuloBiblioteca->setDado( "cod_modulo", $this->obRModulo->getCodModulo() );
                 $this->obTGCModuloBiblioteca->setDado( "cod_biblioteca", $this->obTGCBiblioteca->getDado( "cod_biblioteca" ) );
                 $obErro = $this->obTGCModuloBiblioteca->inclusao( $boTransacao );
            }
        }
    } elseif ( !$obErro->ocorreu() and !$rsModuloBiblioteca->eof() ) {
        $this->obTGCModuloBiblioteca->setDado( "cod_biblioteca", $rsModuloBiblioteca->getCampo( "cod_biblioteca" ) );
        $this->obTGCModuloBiblioteca->setDado( "cod_modulo", $this->obRModulo->getCodModulo() );
    }

    return $obErro;
}

function apagaFuncaoPL($obAtribrutoDinamico, $boTransacao)
{
    $this->verificaModulo();
    $this->obPersistenteCadastro->setDado( "cod_cadastro", $this->getCodCadastro() );
    $obErro = $this->obPersistenteCadastro->recuperaPorChave( $rsPersistenteCadastro, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        include_once( CAM_GA_ADM_MAPEAMENTO.$rsPersistenteCadastro->getCampo('mapeamento').".class.php" );
        $stClasseMapeamento = $rsPersistenteCadastro->getCampo('mapeamento');
        $obPersistenteAtributoValor = new $stClasseMapeamento;
        $obPersistenteAtributoValor->setDado("cod_modulo", $this->obRModulo->getCodModulo() );
        $obPersistenteAtributoValor->setDado("cod_cadastro", $this->getCodCadastro() );
        $arChaveAtributoValor = explode( ",", $obPersistenteAtributoValor->getComplementoChave() );
        $stChaveAtributo = "";
        $arParametros = array();
        foreach ($arChaveAtributoValor as $inIndice => $stCampo) {
            if ($stCampo != "timestamp" AND $stCampo != "cod_cadastro" AND $stCampo != "cod_atributo") {
                $stParametro    = "in".str_replace( " ","", ucwords( str_replace( "_"," ", $stCampo ) ) );
                $arParametros[] = $stParametro;
                $stChaveAtributo .= " AND VALOR.".$stCampo." = \'\'||".$stParametro."||\'\' ";
            }
        }
        $obPersistenteAtributoValor->setDado("stCondicao", $stChaveAtributo );
        $obErro = $obAtribrutoDinamico->consultar( $rsAtributo, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obRModulo->consultar( $rsModulo, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arNomeFuncao["nom_modulo"]   = ucwords( $this->obRModulo->getNomModulo() );
                $arNomeFuncao["nom_cadastro"] = ucwords( $rsPersistenteCadastro->getCampo('nom_cadastro') );
                $arNomeFuncao["nom_atributo"] = ucwords( $obAtribrutoDinamico->getNome() );
                $stNomeFuncao = "recupera";
                $ar1 = array('/ /','/á/','/à/','/â/','/ã/','/é/','/è/','/ê/','/í/','/ì/','/î/','/ó/','/ò/','/õ/','/ô/','/ú/','/ù/','/û/','/ü/','/Á/','/À/','/Â/','/Ã/','/É/','/È/','/Ê/','/Í/','/Ì/','/Î/','/Ó/','/Ò/','/Õ/','/Ô/','/Ú/','/Ù/','/Û/','/Ü/','/ç/','/Ç/');
                $ar2 = array('','a','a','a','a','e','e','e','i','i','i','o','o','o','o','u','u','u','u','A','A','A','A','E','E','E','I','I','I','O','O','O','O','U','U','U','U','c','C');
                foreach ($arNomeFuncao as $stNome) {
                    $stNome = preg_replace( $ar1, $ar2, $stNome );
                    $stNomeFuncao .= $stNome;
                }
                $stSql = $stNomeFuncao."( ";
                foreach ($arParametros as $stNomeParametro) {
                     $stSql .= " INTEGER,";
                }
                $stSql = substr( $stSql, 0, strlen( $stSql ) - 1 )." )";

                $stFiltro = " WHERE nom_funcao = '".$stNomeFuncao."' AND cod_tipo_retorno = 2 ";
                $this->obTGCFuncao = new TGCFuncao;
                $obErro = $this->obTGCFuncao->recuperaTodos( $rsFuncao, $stFiltro,"", $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTGCParametro = new TGCParametro;
                    $this->obTGCParametro->setCampoCod( "cod_funcao" );
                    $this->obTGCParametro->setComplementoChave( "" );
                    $this->obTGCParametro->setDado( "cod_funcao", $rsFuncao->getCampo("cod_funcao") );
                    $obErro = $this->obTGCParametro->exclusao( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->obTGCVariavel = new TGCVariavel;
                        $this->obTGCVariavel->setCampoCod( "cod_funcao" );
                        $this->obTGCVariavel->setComplementoChave( "" );
                        $this->obTGCVariavel->setDado( "cod_funcao", $rsFuncao->getCampo("cod_funcao") );
                        $obErro = $this->obTGCVariavel->exclusao( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $this->obTGCFuncaoInterna = new TGCFuncaoInterna;
                            $this->obTGCFuncaoInterna->setCampoCod( "cod_funcao" );
                            $this->obTGCFuncaoInterna->setComplementoChave( "" );
                            $this->obTGCFuncaoInterna->setDado( "cod_funcao", $rsFuncao->getCampo("cod_funcao") );
                            $obErro = $this->obTGCFuncaoInterna->exclusao( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $this->obTGCFuncao->setCampoCod( "cod_funcao" );
                                $this->obTGCFuncao->setComplementoChave( "" );
                                $this->obTGCFuncao->setDado( "cod_funcao", $rsFuncao->getCampo("cod_funcao") );
                                $obErro = $this->obTGCFuncao->exclusao( $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    $stSql = " DROP FUNCTION ".$stSql;
                                    $obConexao  = new Conexao;
                                    $obErro = $obConexao->executaDML( $stSql, $boTransacao );
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return $obErro;
}

function recuperaAtributoFuncao(&$rsAtributoComFuncao, &$rsAtributoSemFuncao, $boTransacao = "")
{
    $this->obTGCFuncaoAtributo = new TGCFuncaoAtributo;
    $stFiltro = "";
    if ( $this->obRModulo->getCodModulo() ) {
        $stFiltro .= " AND MB.COD_MODULO = ".$this->obRModulo->getCodModulo();
    } else {
        $obErro = new Erro;
        $obErro->setDescricao( "O código do módulo deve ser informado!" );

        return $obErro;
    }
    $obErro = $this->obTGCFuncaoAtributo->recuperaRelacionamento( $rsRelacionamento, $stFiltro, "", $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arFuncaoAtributo = array();
        while ( !$rsRelacionamento->eof() ) {
            $arFuncaoAtributo[$rsRelacionamento->getCampo("cod_atributo")] = $rsRelacionamento->getCampo("nom_atributo");
            $rsRelacionamento->proximo();
        }
        $obErro = $this->recuperaAtributosSelecionados ($rsAtributosSelecionados);
        if ( !$obErro->ocorreu() ) {
            $arAtributoComFuncao = array();
            $arAtributoSemFuncao = array();
            while ( !$rsAtributosSelecionados->eof() ) {
                if ( isset( $arFuncaoAtributo[$rsAtributosSelecionados->getCampo("cod_atributo")] ) ) {
                    $arAtributoComFuncao[] = array( "cod_atributo" => $rsAtributosSelecionados->getCampo("cod_atributo"),
                                                    "nom_atributo" => $arFuncaoAtributo[$rsAtributosSelecionados->getCampo("cod_atributo")] );
                } else {
                    $arAtributoSemFuncao[] = array( "cod_atributo" => $rsAtributosSelecionados->getCampo("cod_atributo"),
                                                    "nom_atributo" => $rsAtributosSelecionados->getCampo("nom_atributo") );
                }
                $rsAtributosSelecionados->proximo();
            }
            $rsAtributoComFuncao = new RecordSet;
            $rsAtributoComFuncao->preenche( $arAtributoComFuncao );
            $rsAtributoSemFuncao = new RecordSet;
            $rsAtributoSemFuncao->preenche( $arAtributoSemFuncao );
        }
    }

    return $obErro;
}

}
