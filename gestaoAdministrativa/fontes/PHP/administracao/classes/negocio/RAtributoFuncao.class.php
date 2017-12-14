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
* Classe de negócio AtributoFuncao
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 5348 $
$Name$
$Author: lizandro $
$Date: 2006-01-19 16:34:56 -0200 (Qui, 19 Jan 2006) $

Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoCadastro.class.php"  );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncao.class.php"    );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoVariavel.class.php"  );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoParametro.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoFuncao.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RAtributoDinamico.class.php" );

class RAtributoFuncao
{
var $inCodFuncao;
var $roRBiblioteca;
var $stNomeFuncao;
var $inCodTipoPrimitivo;
var $roRAtributoDinamico;
var $obTAdministracaoCadastro;
var $obTAdministracaoFuncao;
var $obTAdministracaoAtributoFuncao;
var $obTAdministracaoVariavel;
var $obTAdministracaoParametro;

function setCodFuncao($stValor) { $this->inCodFuncao         = $stValor; }
function setNomeFuncao($stValor) { $this->stNomeFuncao        = $stValor; }
function setCodTipoPrimitivo($stValor) { $this->inCodTipoPrimitivo  = $stValor; }
function setRAtributoDinamico(&$roValor) { $this->roRAtributoDinamico = &$stValor; }

function getCodFuncao() { return $this->inCodFuncao;        }
function getNomeFuncao() { return $this->stNomeFuncao;       }
function getCodTipoPrimitivo() { return $this->inCodTipoPrimitivo; }

function RAtributoFuncao(&$roRBiblioteca)
{
    $this->roRBiblioteca = &$roRBiblioteca;
    $this->roRAtributoDinamico = new RAtributoDinamico;
    $this->roRAtributoDinamico->setRModulo( $this->roRBiblioteca->roRModulo );
    $this->roRAtributoDinamico->setCodCadastro( $this->roRBiblioteca->roRModulo->roRCadastro->getCodCadastro() );
    $this->obTAdministracaoCadastro       = new TAdministracaoCadastro;
    $this->obTAdministracaoFuncao         = new TAdministracaoFuncao;
    $this->obTAdministracaoAtributoFuncao = new TAdministracaoAtributoFuncao;
}

function excluirFuncao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoParametro = new TAdministracaoParametro;
        $stFiltro  = " WHERE ";
        $stFiltro .= "     cod_modulo = ".$this->roRBiblioteca->roRModulo->getCodModulo()." AND ";
        $stFiltro .= "     cod_biblioteca = ".$this->roRBiblioteca->getCodigoBiblioteca()." AND ";
        $stFiltro .= "     cod_funcao = ".$this->getCodFuncao();
        $stOrdem = " ORDER BY ordem ";
        //RECUPERA TODOS OS PARAMETROS PARA MONTAR O NOME DA FUNCAO E EXCLUIR OS MESMOS
        $obErro = $this->obTAdministracaoParametro->recuperaTodos( $rsParametros, $stFiltro, $stOrdem, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $stParametros = "";
            while ( !$rsParametros->eof() ) {
                $stParametros .= "integer,";
                $this->obTAdministracaoParametro->setDado( "cod_modulo",     $rsParametros->getCampo('cod_modulo')     );
                $this->obTAdministracaoParametro->setDado( "cod_biblioteca", $rsParametros->getCampo('cod_biblioteca') );
                $this->obTAdministracaoParametro->setDado( "cod_funcao",     $rsParametros->getCampo('cod_funcao')     );
                $this->obTAdministracaoParametro->setDado( "ordem",          $rsParametros->getCampo('ordem')          );
                $obErro = $this->obTAdministracaoParametro->exclusao( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    break;
                }
                $rsParametros->proximo();
            }
            if ( !$obErro->ocorreu() ) {
                //EXCLUSAO DAS VARIAVEIS
                $this->obTAdministracaoVariavel = new TAdministracaoVariavel;
                $stFiltro  = " WHERE ";
                $stFiltro .= "     cod_modulo = ".$this->roRBiblioteca->roRModulo->getCodModulo()." AND ";
                $stFiltro .= "     cod_biblioteca = ".$this->roRBiblioteca->getCodigoBiblioteca()." AND ";
                $stFiltro .= "     cod_funcao = ".$this->getCodFuncao();
                $obErro = $this->obTAdministracaoVariavel->recuperaTodos( $rsVariaveis, $stFiltro, "", $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    while ( !$rsVariaveis->eof() ) {
                        $this->obTAdministracaoVariavel->setDado( "cod_modulo", $rsVariaveis->getCampo('cod_modulo') );
                        $this->obTAdministracaoVariavel->setDado( "cod_biblioteca", $rsVariaveis->getCampo('cod_biblioteca') );
                        $this->obTAdministracaoVariavel->setDado( "cod_funcao", $rsVariaveis->getCampo('cod_funcao') );
                        $this->obTAdministracaoVariavel->setDado( "cod_variavel", $rsVariaveis->getCampo('cod_variavel') );
                        $obErro = $this->obTAdministracaoVariavel->exclusao( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                        $rsVariaveis->proximo();
                    }
                    //EXCLUSAO DA TABELA ATRIBUTO FUNCAO
                    if ( !$obErro->ocorreu() ) {
                        $this->obTAdministracaoAtributoFuncao->setDado( "cod_modulo", $this->roRBiblioteca->roRModulo->getCodModulo() );
                        $this->obTAdministracaoAtributoFuncao->setDado( "cod_biblioteca", $this->roRBiblioteca->getCodigoBiblioteca() );
                        $this->obTAdministracaoAtributoFuncao->setDado( "cod_cadastro", $this->roRAtributoDinamico->getCodCadastro()  );
                        $this->obTAdministracaoAtributoFuncao->setDado( "cod_atributo", $this->roRAtributoDinamico->getCodAtributo()  );
                        $this->obTAdministracaoAtributoFuncao->setDado( "cod_funcao", $this->getCodFuncao() );
                        $obErro = $this->obTAdministracaoAtributoFuncao->exclusao( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            //RECUPERA O NOME DA FUNCAO
                            $this->obTAdministracaoFuncao->setDado( "cod_modulo", $this->roRBiblioteca->roRModulo->getCodModulo() );
                            $this->obTAdministracaoFuncao->setDado( "cod_biblioteca", $this->roRBiblioteca->getCodigoBiblioteca() );
                            $this->obTAdministracaoFuncao->setDado( "cod_funcao", $this->getCodFuncao() );
                            $obErro = $this->obTAdministracaoFuncao->recuperaPorChave( $rsFuncao, $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                //EXCLUI A FUNCAO
                                $obErro = $this->obTAdministracaoFuncao->exclusao( $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    //MONTA A STRIND PARA EXCLUIR A FUNCAO DO BANCO
                                   $stParametros = substr( $stParametros, 0, strlen( $stParametros ) - 1 );
                                   $stSql = " DROP FUNCTION ".$rsFuncao->getCampo( "nom_funcao" )."(".$stParametros.")";
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
    //INFORMAR A CLASSE DE MAPEAMENTO DE FUNCAO
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro  );

    return $obErro;
}

function salvarFuncao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->roRAtributoDinamico->consultar( $rsAtributo, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            //RECUPERA O NOME DA CLASSE DE MAPEAMENTO DE ATRIBUTO VALOR
            $stFiltro  = "     AND c.cod_modulo = ".$this->roRAtributoDinamico->obRModulo->getCodModulo()."\n";
            $stFiltro .= "     AND c.cod_cadastro = ".$this->roRAtributoDinamico->getCodCadastro()."\n";
            $obErro = $this->obTAdministracaoCadastro->recuperaRelacionamento( $rsConfiguracaoCadastro, $stFiltro, '', $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $stMapeamentoValor =  $rsConfiguracaoCadastro->getCampo("mapeamento");
                if ($stMapeamentoValor) {
                    //FAZ A INCLUSAO E INSTANCIA A CLASSE DE MAPEAMENTO
                    $stCaminhoMapeamento  = '';
                    $stCaminhoMapeamento .= $rsConfiguracaoCadastro->getCampo('nom_diretorio_gestao');
                    $stCaminhoMapeamento .= $rsConfiguracaoCadastro->getCampo('nom_diretorio_modulo');
                    $stCaminhoMapeamento .= 'classes/mapeamento/';
                    $stCaminhoMapeamento .= $rsConfiguracaoCadastro->getCampo('mapeamento');
                    include_once( $stCaminhoMapeamento.'.class.php' );
                    $stMapeamentoValor = str_replace( ".class.php", "", $stMapeamentoValor );
                    $obMapeamentoValor = new $stMapeamentoValor;
                    $obMapeamentoValor->setDado("cod_modulo", $this->roRAtributoDinamico->obRModulo->getCodModulo() );
                    $obMapeamentoValor->setDado("cod_cadastro", $this->roRAtributoDinamico->getCodCadastro() );

                    //MONTA O FILTRO COM OS PARAMETROS QUE DEVEM SER PASSADOS PARA FUNCAO
                    $arChaveMapeamentoValor = explode( ",", $obMapeamentoValor->getComplementoChave() );
                    $stChaveAtributo = "";
                    $arParametros = array();
                    foreach ($arChaveMapeamentoValor as $inIndice => $stCampo) {
                        if ($stCampo != "timestamp" AND $stCampo != "cod_cadastro" AND $stCampo != "cod_atributo" AND $stCampo != "cod_modulo") {
                            $stParametro    = "in".str_replace( " ","", ucwords( str_replace( "_"," ", $stCampo ) ) );
                            $arParametros[] = $stParametro;
                            $stChaveAtributo .= " AND VALOR.".$stCampo." = \'\'||".$stParametro."||\'\' \n";
                        }
                    }
                    $stChaveAtributo .= " AND ACA.cod_atributo = ".$this->roRAtributoDinamico->getCodAtributo();

                    //SETA A CONDIÇÃO PARA A CONSULTA QUE SERA USADA PELA FUNÇÃO
                    $obMapeamentoValor->setDado("stCondicao", $stChaveAtributo );

                    //CONSULTA O ATRIBUTO PARA MONTAR O NOME DA FUNÇÃO
                    $stNomModulo = $this->roRAtributoDinamico->obRModulo->getNomModulo();
                    if ( empty( $stNomModulo ) ) {
                        $obErro = $this->roRAtributoDinamico->obRModulo->consultar( $rsModulo, $boTransacao );
                    }
                    if ( !$obErro->ocorreu() ) {
                        $arNomeFuncao["nom_modulo"]   = $this->roRAtributoDinamico->obRModulo->getNomModulo();
                        $arNomeFuncao["nom_cadastro"] = $rsConfiguracaoCadastro->getCampo( "nom_cadastro" );
                        $arNomeFuncao["nom_atributo"] = $this->roRAtributoDinamico->getNome();
                        $stNomeFuncao = "recupera";
                        $ar1 =
array('/á/','/à/','/â/','/ã/','/ä/','/ë/','/é/','/è/','/ê/','/ï/','/í/','/ì/','/î/','/ö/','/ó/','/ò/','/õ/','/ô/','/ú/','/ù/','/û/','/ü/','/Á/','/À/','/Â/','/Ã/','/Ë/','/É/','/È/','/Ê/','/Ï/','/Í/','/Ì/','/Î/','/Ö/','/Ó/','/Ò/','/Õ/','/Ô/','/Ú/','/Ù/','/Û/','/Ü/','/ç/','/Ç/','/ÿ/');
                        $ar2 =
array('a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','A','A','A','A','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','c','C','y');

                        foreach ($arNomeFuncao as $stNome) {
                            $arNome = explode( " ", $stNome );
                            $stTmpNomeFuncao = "";
                            foreach ($arNome as $stTmpNome) {
                                $stTmpNome = preg_replace( $ar1, $ar2, $stTmpNome );
                                $stTmpNome = preg_replace("/[^0-9a-zA-Z]/","" ,$stTmpNome );
                                $stTmpNomeFuncao .= ucwords( $stTmpNome );
                            }
                            $stNomeFuncao .= $stTmpNomeFuncao;
                        }
                        if (Sessao::read('stNomeFuncoes')) {
                            $stNomeFuncoes = Sessao::read('stNomeFuncoes').",".$stNomeFuncao;
                        }
                        Sessao::write('stNomeFuncoes',$stNomeFuncoes);
                        //echo $stNomeFuncao;

                        //MONTA O CORPO DA FUNCAO
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
                        $stSql .= " stSql := ''".str_replace( "''", "''''''''",$obMapeamentoValor->montaRecuperaAtributosSelecionadosValores())." AND ACA.cod_atributo = ".$this->roRAtributoDinamico->getCodAtributo().";'';";
                        $stSql = str_replace( "\'\'||", "''||", $stSql );
                        $stSql = str_replace( "||\'\'", "||''", $stSql );
                        $stSql .=  "OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    ' LANGUAGE plpgsql;";
                        //INCLUI A FUNÇÃO GERA OS PARAMETROS E GERA A FUNÇÃO NO BANCO
                        $this->obTAdministracaoFuncao->setDado( "cod_modulo",$this->roRBiblioteca->roRModulo->getCodModulo() );
                        $this->obTAdministracaoFuncao->setDado( "cod_biblioteca", $this->roRBiblioteca->getCodigoBiblioteca() );
                        $this->obTAdministracaoFuncao->setDado( "cod_tipo_retorno",2 );
                        $obErro = $this->obTAdministracaoFuncao->proximoCod( $inCodFuncao, $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $this->obTAdministracaoFuncao->setDado( "cod_funcao", $inCodFuncao  );
                            $this->obTAdministracaoFuncao->setDado( "nom_funcao", $stNomeFuncao );
                            $obErro = $this->obTAdministracaoFuncao->inclusao( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $this->obTAdministracaoAtributoFuncao->setDado( "cod_modulo", $this->roRBiblioteca->roRModulo->getCodModulo() );
                                $this->obTAdministracaoAtributoFuncao->setDado( "cod_biblioteca", $this->roRBiblioteca->getCodigoBiblioteca() );
                                $this->obTAdministracaoAtributoFuncao->setDado( "cod_cadastro", $this->roRAtributoDinamico->getCodCadastro()  );
                                $this->obTAdministracaoAtributoFuncao->setDado( "cod_atributo", $this->roRAtributoDinamico->getCodAtributo()  );
                                $this->obTAdministracaoAtributoFuncao->setDado( "cod_tipo", 2 );
                                $this->obTAdministracaoAtributoFuncao->setDado( "cod_funcao", $inCodFuncao );
                                $obErro = $this->obTAdministracaoAtributoFuncao->inclusao( $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    foreach ($arParametros as $stNomeParametro) {
                                        $this->obTAdministracaoVariavel = new TAdministracaoVariavel;
                                        $this->obTAdministracaoVariavel->setDado( "cod_modulo",$this->roRBiblioteca->roRModulo->getCodModulo() );
                                        $this->obTAdministracaoVariavel->setDado( "cod_biblioteca",$this->roRBiblioteca->getCodigoBiblioteca() );
                                        $this->obTAdministracaoVariavel->setDado( "cod_tipo",1 );
                                        $this->obTAdministracaoVariavel->setDado( "cod_funcao",$inCodFuncao  );
                                        $obErro = $this->obTAdministracaoVariavel->proximoCod( $inCodVariavel, $boTransacao );
                                        if ( !$obErro->ocorreu() ) {
                                            $this->obTAdministracaoVariavel->setDado( "cod_variavel", $inCodVariavel );
                                            $this->obTAdministracaoVariavel->setDado( "nom_variavel", $stNomeParametro );
                                            $this->obTAdministracaoVariavel->setDado( "valor_inicial","" );
                                            $obErro = $this->obTAdministracaoVariavel->inclusao( $boTransacao );
                                            if ( !$obErro->ocorreu() ) {
                                                $this->obTAdministracaoParametro = new TAdministracaoParametro;
                                                $this->obTAdministracaoParametro->setDado( "cod_modulo",$this->roRBiblioteca->roRModulo->getCodModulo());
                                                $this->obTAdministracaoParametro->setDado( "cod_biblioteca", $this->roRBiblioteca->getCodigoBiblioteca() );
                                                $this->obTAdministracaoParametro->setDado( "cod_tipo", 1 );
                                                $this->obTAdministracaoParametro->setDado( "cod_funcao", $inCodFuncao );
                                                $this->obTAdministracaoParametro->setDado( "cod_variavel", $inCodVariavel );
                                                $this->obTAdministracaoParametro->setDado( "ordem",++$inOrdem );
                                                $obErro = $this->obTAdministracaoParametro->inclusao( $boTransacao );
                                                if ( $obErro->ocorreu() ) {
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
                } else {
                    $obErro->setDescricao( "Dever ser cadastrado no banco a classe de mapeamento de valor do atributo." );
                }
            }
        }
    }
    //INFORMAR A CLASSE DE MAPEAMENTO DE FUNCAO
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro  );

    return $obErro;
}

function listarParametros(&$rsParametros, $boTransacao = '')
{
    $stFiltro  = " AND var.cod_modulo     = ".$this->roRBiblioteca->roRModulo->getCodModulo();
    $stFiltro .= " AND var.cod_biblioteca = ".$this->roRBiblioteca->getCodigoBiblioteca();
    $stFiltro .= " AND var.cod_tipo       = ".$this->getCodTipoPrimitivo();
    $stFiltro .= " AND var.cod_funcao     = ".$this->getCodFuncao();
    $stOrdem = " ORDER BY ordem ";
    $this->obTAdministracaoParametro = new TAdministracaoParametro;
    $obErro = $this->obTAdministracaoParametro->recuperaRelacionamento( $rsParametros, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

}
?>
