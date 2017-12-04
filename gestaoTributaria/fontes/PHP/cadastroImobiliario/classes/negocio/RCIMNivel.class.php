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
     * Classe de regra de negócio para nível
     * Data de Criação: 08/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMNivel.class.php 63826 2015-10-21 16:39:23Z arthur $

     * Casos de uso: uc-05.01.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMVigencia.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMNivel.class.php"    );
//INCLUD DAS CLASSES PARA  O TRATAMNTO DOS ATRIBUTOS
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"           );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoNivelValor.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoNivel.class.php" );

class RCIMNivel
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoNivel;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoVigencia;
/**
    * @access Private
    * @var String
*/
var $stNomeNivel;
/**
    * @access Private
    * @var String
*/
var $stMascara;
/**
    * @access Private
    * @var String
*/
var $dtInicioVigencia;
/**
    * @access Private
    * @var Object
*/
var $obTCIMVigencia;
/**
    * @access Private
    * @var Object
*/
var $obTCIMNivel;
/**
    * @access Private
    * @var Object
*/
var $obRCdastroDinamico;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * @access Public
    * @param String $valor
*/
function setCodigoNivel($valor) { $this->inCodigoNivel     = $valor;         }
/**
    * @access Public
    * @param String $valor
*/
function setCodigoVigencia($valor) { $this->inCodigoVigencia  = $valor;         }
/**
    * @access Public
    * @param String $valor
*/
function setNomeNivel($valor) { $this->stNomeNivel  = $valor;              }
/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara  = $valor;               }
/**
    * @access Public
    * @param String $valor
*/
function setInicioVigencia($valor) { $this->dtInicioVigencia  = $valor;        }
/**
    * @access Public
    * @param String $valor
*/
function setTCIMVigencia($valor) { $this->obTCIMNivel  = $valor;             }
/**
    * @access Public
    * @param String $valor
*/
function setTCIMNivel($valor) { $this->obTCIMVigencia  = $valor;          }
/**
    * @access Public
    * @param String $valor
*/
function setRCadastroDinamico($valor) { $this->obRCdastroDinamico = $valor;       }

/**
    * @access Public
    * @return Integer
*/
function getCodigoNivel() { return $this->inCodigoNivel;            }
/**
    * @access Public
    * @return Integer
*/
function getCodigoVigencia() { return $this->inCodigoVigencia;         }
/**
    * @access Public
    * @return String
*/
function getNomeNivel() { return $this->stNomeNivel;              }
/**
    * @access Public
    * @return String
*/
function getMascara() { return $this->stMascara;                }
/**sD
    * @access Public
    * @return String
*/
function getInicioVigencia() { return $this->dtInicioVigencia;         }
/**
    * @access Public
    * @return Object
*/
function getTCIMVigencia() { return $this->obTCIMVigencia;           }
/**
    * @access Public
    * @return Object
*/
function getTCIMNivel() { return $this->obTCIMNivel;              }
/**
    * @access Public
    * @return Object
*/
function getRCadastroDinamico() { return $this->obRCdastroDinamico;       }

/**
     * Método construtor
     * @access Private
*/
function RCIMNivel()
{
    $this->obTCIMVigencia = new TCIMVigencia;
    $this->obTCIMNivel    = new TCIMNivel;
    $this->obTransacao    = new Transacao;
    $this->obRCadastroDinamico  = new RCadastroDinamico;
    $this->obRCadastroDinamico->setPersistenteValores   ( new TCIMAtributoNivelValor );
    $this->obRCadastroDinamico->setPersistenteAtributos ( new TCIMAtributoNivel );
    $this->obRCadastroDinamico->setCodCadastro( 1 );
    $this->obRCadastroDinamico->obRModulo->setCodModulo( 12 );
}

/**
    * Inclui os dados setados na tabela de Nivel
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirNivel($boTransacao = "")
{
    include_once( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php" );

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obRCIMLocalizacao = new RCIMLocalizacao();
        $obRCIMLocalizacao->setCodigoVigencia( $this->inCodigoVigencia );
        $obRCIMLocalizacao->listarLocalizacao( $rsLocalizacao, $boTransacao );
        if ( !$rsLocalizacao->eof() ) {
            $obErro = new Erro;

            $obErro->setDescricao("Existem localizações cadastradas com a hierarquia atual. Não é possível incluir novos níveis!");
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMNivel );

            return $obErro;
        }

        //Verifica se existe vigencia cadastrada, caso mão exista faz o cadastro
        if (!$this->inCodigoVigencia) {
            $this->dtInicioVigencia = date("d/m/Y", time() );
            $obErro = $this->incluirVigencia( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->validaNomeNivel( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMNivel->setDado( "cod_vigencia", $this->inCodigoVigencia );
                $obErro = $this->obTCIMNivel->proximoCod( $this->inCodigoNivel, $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTCIMNivel->setDado( "cod_nivel"   , $this->inCodigoNivel    );
                    $this->obTCIMNivel->setDado( "nom_nivel"   , $this->stNomeNivel      );
                    $this->obTCIMNivel->setDado( "mascara"     , $this->stMascara        );
                    $obErro = $this->obTCIMNivel->inclusao( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        //O Restante dos valores vem setado da página de processamento
                        $arChaveAtributoNivel =  array( "cod_nivel" => $this->inCodigoNivel,
                                                        "cod_vigencia" => $this->inCodigoVigencia );
                        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoNivel );
                        $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMNivel );

    return $obErro;
}

/**
    * Altera os dados do Nivel selecionado no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarNivel($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //O Restante dos valores vem setado da página de processamento
        $arChaveAtributoNivel =  array( "cod_nivel" => $this->inCodigoNivel, "cod_vigencia" => $this->inCodigoVigencia );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoNivel );
        $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMNivel->setDado( "cod_nivel"   , $this->inCodigoNivel    );
            $this->obTCIMNivel->setDado( "cod_vigencia", $this->inCodigoVigencia );
            $this->obTCIMNivel->setDado( "nom_nivel"   , $this->stNomeNivel      );
            $obErro = $this->validaNomeNivel( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMNivel->setDado( "mascara", $this->stMascara        );
                $obErro = $this->obTCIMNivel->alteracao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMNivel );

    return $obErro;
}

/**
    * Altera os dados da Vigência selecionado no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarVigencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMVigencia->setDado( "dt_inicio"   , $this->dtInicioVigencia );
        $this->obTCIMVigencia->setDado( "cod_vigencia", $this->inCodigoVigencia );
        $obErro = $this->obTCIMVigencia->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMVigencia );

    return $obErro;
}

/**
    * Exclui o Nivel selecionado do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirNivel($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->recuperaProximoNivel( $rsProximoNivel );
        if ( $rsProximoNivel->getNumLinhas() > 0 ) {
            $obErro->setDescricao("Nível ".$this->getCodigoNivel()." não é o último da hierarquia!");
        } else {
            $arChaveAtributoNivel =  array( "cod_nivel" => $this->inCodigoNivel, "cod_vigencia" => $this->inCodigoVigencia );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoNivel );
            $obErro = $this->obRCadastroDinamico->excluir( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMNivel->setDado( "cod_nivel"   , $this->inCodigoNivel    );
                $this->obTCIMNivel->setDado( "cod_vigencia", $this->inCodigoVigencia );
                $obErro = $this->obTCIMNivel->exclusao( $boTransacao );
            }
            if ( $obErro->ocorreu() ) {
                $obErro->setDescricao("Nível ".$this->getCodigoNivel()." possui referências cadastradas no sistema!");
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMNivel );

    return $obErro;
}

/**
    * Exclui o Vigencia Atividade selecionado do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirVigencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMVigencia->setDado( "dt_inicio"   , $this->dtInicioVigencia );
        $this->obTCIMVigencia->setDado( "cod_vigencia", $this->inCodigoVigencia );
        $obErro = $this->obTCIMVigencia->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMVigencia);

    return $obErro;
}

/**
    * Recupera do banco de dados os dados do Nivel selecionado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarNivel($boTransacao = "")
{
    $this->obTCIMNivel->setDado( "cod_nivel"   , $this->inCodigoNivel    );
    $this->obTCIMNivel->setDado( "cod_vigencia", $this->inCodigoVigencia );
    $obErro = $this->obTCIMNivel->recuperaPorChave( $rsNivel, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stNomeNivel = $rsNivel->getCampo( "nom_nivel" );
        $this->stMascara   = $rsNivel->getCampo( "mascara"   );
    }

    return $obErro;
}

/**
    * Recupera do banco de dados ultima data cadastrada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarDataUltimaVigencia(&$rsDataUltimaVigencia, $boTransacao = "")
{
    $obErro = $this->obTCIMVigencia->recuperaDataUltimaVigencia( $rsDataUltimaVigencia, $boTransacao );

    return $obErro;
}
/**
    * Recupera Mascara para Vigencia Atual
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function mascaraNivelVigenciaAtual(&$stMascara,$boTransacao = "")
{
    $obErro         = $this->recuperaVigenciaAtual($rsVigenciaAtual);
    $inCodVigencia  = $rsVigenciaAtual->getCampo("cod_vigencia");
    $this->inCodVigencia = $inCodVigencia;
    $obErro         = $this->recuperaMascaraNiveis($rsNivel);
    $stMascara      = "";
    while ( !$rsNivel->eof() ) {
        $stMascara .= $rsNivel->getCampo("mascara").".";
        $rsNivel->proximo();
    }
    $stMascara = substr($stMascara,0,strlen($stMascara)-1);
    unset($this->inCodVigencia);

    return $obErro;
}
/**
    * Lista os Niveis segundo o filtro setado
    * @access Public
    * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNiveis(&$rsNivel, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoNivel) {
        $stFiltro .= " niv.COD_NIVEL = ".$this->inCodigoNivel." AND";
    }
    if ($this->inCodigoVigencia) {
        $stFiltro .= " niv.COD_VIGENCIA = ". $this->inCodigoVigencia." AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY niv.COD_VIGENCIA, niv.COD_NIVEL ";
    $obErro = $this->obTCIMNivel->recuperaRelacionamentoConsulta( $rsNivel, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Gera a mascara segundo o filtro setado
    * @access Public
    * @param  Object $stMascara String com a mascara
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function geraMascara(&$stMascara , $boTransacao = "")
{
    $obErro = $this->listarNiveis( $rsNivel, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stMascara = "";
        while ( !$rsNivel->eof() ) {
            $stMascara .= $rsNivel->getCampo( "mascara" ).".";
            $rsNivel->proximo();
        }
        if ($stMascara) {
            $stMascara = substr( $stMascara, 0, strlen( $stMascara ) - 1);
        }
    }

    return $obErro;
}

/**
    * recupera a mascara de cada nivel
    * @access Public
    * @param  Object $stMascara String com a mascara
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaMascaraNiveis(&$rsNivel , $boTransacao = "")
{
    if ($this->inCodigoVigencia) {
        $stFiltro = ' WHERE COD_VIGENCIA = '. $this->inCodigoVigencia ;
    }
    if (!isset($stOrdem)) {
        $stOrdem= "";
    }
    if (!isset($stFiltro)) {
        $stFiltro= "";
    }
    $obErro = $this->obTCIMNivel->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os Niveis anteriores segundo o nivel setado
    * @access Public
    * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNiveisAnteriores(&$rsNivel, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoNivel) {
        $stFiltro .= " COD_NIVEL < ".$this->inCodigoNivel." AND";
    }
    if ($this->inCodigoVigencia) {
        $stFiltro .= " COD_VIGENCIA = ". $this->inCodigoVigencia." AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY COD_VIGENCIA, COD_NIVEL ";
    $obErro = $this->obTCIMNivel->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function validaNomeNivel($boTransacao = "")
{
    $stFiltro .= " WHERE UPPER( NOM_NIVEL ) ";
    $stFiltro .= " = UPPER( '".$this->stNomeNivel."' ) ";
    $stFiltro .= " AND COD_VIGENCIA = ".$this->inCodigoVigencia;
    if ($this->inCodigoNivel AND $this->inCodigoVigencia) {
        $stFiltro .= " AND COD_NIVEL <> ".$this->inCodigoNivel;
    }
    $stOrdem = "";
    $obErro = $this->obTCIMNivel->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsNivel->eof() ) {
        $obErro->setDescricao( "Já existe outro nível cadastrado com o nome ".$this->stNomeNivel."!" );
    }

    return $obErro;
}

function listarVigencias(&$rsVigencia, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
       $stFiltro .= " COD_VIGENCIA = ". $this->inCodigoVigencia." AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    $stOrdem = "";
    $obErro = $this->obTCIMVigencia->recuperaTodos( $rsVigencia, $stFiltro , $stOrdem, $boTransacao );

   return $obErro;
}

/**
    * Inclui os dados setados na tabela de vigencia
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirVigencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTCIMVigencia->proximoCod( $this->inCodigoVigencia, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            echo "Cod Vigencia=".$this->inCodigoVigencia;
            $this->obTCIMVigencia->setDado( "cod_vigencia", $this->inCodigoVigencia );
            $this->obTCIMVigencia->setDado( "dt_inicio", $this->dtInicioVigencia );
            $obErro = $this->obTCIMVigencia->inclusao ( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Recupera a vigencia do periodo atual
    * @access Public
    * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaVigenciaAtual(&$rsRecordSet, $boTransacao = "")
{
    $obErro = $this->obTCIMVigencia->recuperaVigenciaAtual( $rsRecordSet,  $boTransacao );

    return $obErro;
}

/**
    * Recupera o ultimo nivel da vigencia setada
    * @access Public
    * @param  Object $rsRecordSet Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaUltimoNivel(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " cod_vigencia = ".$this->inCodigoVigencia." AND ";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " cod_nivel < ".$this->inCodigoNivel." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem  = " ORDER BY ";
    $stOrdem .= "     cod_nivel ";
    $stOrdem .= " DESC ";
    $stOrdem .= " LIMIT 1 ";
    $obErro = $this->obTCIMNivel->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Recupera o ultimo nivel da vigencia corrente
    *l @access Public
    * @param  Object $rsRecordSet Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaUltimoNivelAtual(&$rsRecordSet , $boTransacao = "")
{
    $rsRecordSet = new RecordSet;
    $obErro = $this->recuperaVigenciaAtual( $rsVigencia, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsVigencia->eof() ) {
        $this->inCodigoVigencia = $rsVigencia->getCampo( "cod_vigencia" );
        $obErro = $this->recuperaUltimoNivel( $rsRecordSet, $boTransacao );
    }

    return $obErro;
}

/**
    * Recupera o proximo nivel da vigencia em relacao ao nivel
    * @access Public
    * @param  Object $rsRecordSet Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaProximoNivel(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " cod_vigencia = ".$this->inCodigoVigencia." AND ";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " cod_nivel > ".$this->inCodigoNivel." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem  = " ORDER BY ";
    $stOrdem .= "     cod_nivel ";
    $stOrdem .= " LIMIT 1 ";
    $obErro = $this->obTCIMNivel->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

}
?>
