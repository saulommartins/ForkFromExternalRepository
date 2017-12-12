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
* Classe de regra de negócio Candidato
* Data de Criação: 30/03/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Rafael Almeida
* @author Desenvolvedor: João Rafael tissot (27/04/2005)

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.01.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_CON_MAPEAMENTO."TConcursoCandidato.class.php"                                   );
include_once ( CAM_GRH_CON_MAPEAMENTO."TConcursoConcursoCandidato.class.php"                           );
include_once ( CAM_GA_ADM_MAPEAMENTO ."TAdministracaoUF.class.php"                                     );
include_once ( CAM_GA_ADM_MAPEAMENTO ."TAdministracaoMunicipio.class.php"                              );
include_once ( CAM_GA_CGM_NEGOCIO    ."RCGMPessoaFisica.class.php"                                     );
include_once ( CAM_GRH_CON_NEGOCIO   ."RConcursoConcurso.class.php"		                       );
include_once ( CAM_GA_ADM_NEGOCIO    ."RCadastroDinamico.class.php"                                    );
include_once ( CAM_GRH_CON_MAPEAMENTO."TConcursoAtributoCandidatoValor.class.php"                      );

class RConcursoCandidato extends RCGMPessoaFisica
{
/**
    * @access Private
    * @var Integer
*/
var $inCodCandidato;
/**
    * @access Private
    * @var Integer
*/
var $inClassificacao;
/**
    * @access Private
    * @var Numeric
*/
var $boAprovado;
/**
    * @access Private
    * @var Integer
*/
var $inNotaProva;
/**
    * @access Private
    * @var Integer
*/
var $inNotaTitulo;
/**
    * @access Private
    * @var Boolean
*/
var $boReclassificacao;
/**
    * @access Private
    * @var Object
*/
var $obTConcursoCandidato;
/**
    * @access Private
    * @var Object
*/
var $obTConcursoConcursoCandidato;
/**
    * @access Private
    * @var Object
*/
var $obRConcursoConcurso;
/**
    * @access Private
    * @var Object
*/
var $obRCadastroDinamico;
/**
    * @access Public
    * @param Integer $valor
*/
function setCodCandidato($valor) { $this->inCodCandidato		= $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodCargo($valor) { $this->inCodCargo		= $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setClassificacao($valor) { $this->inClassificacao		= $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setAprovado($valor) { $this->boAprovado        	= $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNotaProva($valor) { $this->inNotaProva        	= $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNotaTitulo($valor) { $this->inNotaTitulo        	= $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setReclassificacao($valor) { $this->boReclassificacao   	= $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao          = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTConcursoCandidato($valor) { $this->obTConcursoCandidato         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTConcursoConcursoCandidato($valor) { $this->obTConcursoConcursoCandidato = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRConcursoConcurso($valor) { $this->obRConcursoConcurso  = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodCandidato() { return $this->inCodCandidato;                  }
/**
    * @access Public
    * @return Integer
*/
function getCodCargo() { return $this->inCodCargo;                  }
/**
    * @access Public
    * @return Integer
*/

function getClassificacao() { return $this->inClassificacao;                  }
/**
    * @access Public
    * @return Integer
*/
function getAprovado() { return $this->boAprovado;                       }
/**
    * @access Public
    * @return Integer
*/
function getNotaProva() { return $this->inNotaProva;                       }
/**
    * @access Public
    * @return Integer
*/
function getNotaTitulo() { return $this->inNotaTitulo;                      }
/**
    * @access Public
    * @return Boolean
*/
function getReclassificacao() { return $this->boReclassificacao;                  }
/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                        }
/**
    * @access Public
    * @return Object
*/
function getTConcursoCandidato() { return $this->obTConcursoCandidato          ;           }
/**
    * @access Public
    * @return Object
*/
function getTConcursoConcursoCandidato() { return $this->obTConcursoConcursoCandidato  ;           }
/**
    * @access Public
    * @return Object
*/
function getRConcursoConcurso() { return $this->obRConcursoConcurso;              }

/**
     * Método construtor
     * @access Private
*/
function RConcursoCandidato()
{
    parent::RCGMPessoaFisica();
    $this->obTUF                  			            = new TUF                      	    ;
    $this->obTMunicipio            			            = new TMunicipio               	    ;
    $this->setTConcursoCandidato   			            ( new TConcursoCandidato       	    );
    $this->setTConcursoConcursoCandidato	            ( new TConcursoConcursoCandidato 	);
    $this->setTransacao    				                ( new Transacao    	  		        );
    $this->setRConcursoConcurso			                ( new RConcursoConcurso		        );
    $this->obRCadastroDinamico                          = new RCadastroDinamico;
    $this->obRCadastroDinamico->setPersistenteValores   ( new TConcursoAtributoCandidatoValor );
    $this->obRCadastroDinamico->setCodCadastro          ( 2 );
    $this->obRCadastroDinamico->obRModulo->setCodModulo ( 17 );
}
/**
    * Incluir dados do Candidato no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirCandidato($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $obErro = $this->listarCandidato( $rsCandidatoConcurso );
    if ( ($rsCandidatoConcurso->getCampo("numcgm") != $this->getNumCGM()) && ($rsCandidatoConcurso->getCampo("cod_edital") != $this->obRConcursoConcurso->getCodEdital())) {
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTConcursoCandidato->proximoCod( $inCodCandidato, $boTransacao );
            $this->setCodCandidato( $inCodCandidato );
            if ( !$obErro->ocorreu() ) {
                $this->obTConcursoCandidato->setDado("cod_candidato", $this->getCodCandidato()  );
                $this->obTConcursoCandidato->setDado("numcgm",        $this->getNumCGM()        );
                $obErro = $this->obTConcursoCandidato->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTConcursoConcursoCandidato->setDado("cod_edital", $this->obRConcursoConcurso->getCodEdital() );
                    $this->obTConcursoConcursoCandidato->setDado("cod_candidato", $this->getCodCandidato() );
                    $this->obTConcursoConcursoCandidato->setDado("cod_cargo", $this->getCodCargo() );
                    $obErro = $this->obTConcursoConcursoCandidato->inclusao( $boTransacao );

                    /*if ( !$obErro->ocorreu() ) {
                     //O Restante dos valores vem setado da página de processamento
                     $arChaveAtributoCandidato =  array( "cod_candidato" => $this->getCodCandidato() );
                     $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
                     $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
                    }*/
                }
            }
        }
    } else {
        $obErro->setDescricao("Candidato já cadastrado para este concurso".Sessao::getEntidade()."!");

        return $obErro;
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConcursoCandidato);

    return $obErro;
}

/**
    * Inclui dados referentes a classificacao do candidato
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function classificarCandidato($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
            $this->obTConcursoCandidato->setDado("cod_candidato",  $this->getCodCandidato() );
            $this->obTConcursoCandidato->setDado("numcgm",         $this->getNumCGM()       );
            $this->obTConcursoCandidato->setDado("nota_prova",     $this->getNotaProva()    );
            if( $this->getNotaTitulo() )
                $this->obTConcursoCandidato->setDado("nota_titulacao", $this->getNotaTitulo()    );
            if( $this->getClassificacao() )
                $this->obTConcursoCandidato->setDado("classificacao",  $this->getClassificacao());
            $obErro = $this->obTConcursoCandidato->alteracao( $boTransacao );

    } else {
        $obErro->setDescricao("Candidato já classificado para este concurso".Sessao::getEntidade()."!");

        return $obErro;
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConcursoCandidato);

    return $obErro;
}

/**
    * Reclassifica o candidato, alterando a tabela TCandidato
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function reclassificarCandidato($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTConcursoCandidato->setDado    ( "cod_candidato"   , $this->getCodCandidato()                    );
        $this->obTConcursoCandidato->setDado    ( "cod_concurso"    , $this->obRConcursoConcurso->getCodEdital()  );
        $this->obTConcursoCandidato->setDado    ( "numcgm"          , $this->getNumCGM()                          );
        $this->obTConcursoCandidato->setDado    ( "reclassificado"  , $this->getReclassificacao()                 );
        $obErro = $this->obTConcursoCandidato->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConcursoCandidato);

    return $obErro;
}

/**
* Executa um consultaPorChave na classe Persistente ConcursoCandidato
* @access Public
* @param  String $stOrdem Parâmetro de Ordenação
* @param  Object $boTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarCandidato(&$rsRecordSet, $boTransacao = "")
{
    if ( $this->getNumCGM() ) {
        $stFiltro = "  AND cc.numcgm = ".$this->getNumCGM()." and ccc.cod_edital = ".$this->obRConcursoConcurso->getCodEdital();
    }
    if ( $this->getCodCandidato() ) {
        $stFiltro .= "  AND cc.cod_candidato = ".$this->getCodCandidato() ;
    }
    $obErro = $this->obTConcursoCandidato->recuperaCandidatoConcurso( $rsRecordSet, $stFiltro, "", $boTransacao );

    return $obErro;
}

/**
* Executa um recuperaTodos na classe Persistente ConcursoCandidato
* @access Public
* @param  String $stOrdem Parâmetro de Ordenação
* @param  Object $boTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarCandidatoPorEdital(&$rsRecordSet,$stFiltro = "",$stOrder = "", $boTransacao = "")
{
    if( $this->obRConcursoConcurso->getCodEdital() ) {
        $stFiltro .= "  AND ccc.cod_edital = ".$this->obRConcursoConcurso->getCodEdital();
    }
    
    $stOrderAux = " ORDER BY cc.reclassificado,t.media DESC ".$stOrder;
    $stOrder = $stOrderAux;
    $obErro = $this->obTConcursoCandidato->recuperaCandidatoConcurso( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Executa um recuperaTodos na classe Persistente ConcursoCandidato
* @access Public
* @param  String $stOrdem Parâmetro de Ordenação
* @param  Object $boTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarCandidatoPorCodigo(&$rsRecordSet,$stFiltro = "",$stOrder = "", $boTransacao = "")
{
    if( $this->getNumCGM() )
        $stFiltro .= "  AND cc.numcgm = ".$this->getNumCGM();
    if( $this->getCodCandidato() )
        $stFiltro .= " AND cc.cod_candidato = ".$this->getCodCandidato();
    $obErro = $this->obTConcursoCandidato->recuperaCandidatoConcurso( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

//Métodos de interface

/**
* Executa um recuperaTodos na classe Persistente UF
* @access Public
* @param  Object $rsListaCategoria Retorna o RecordSet preenchido
* @param  String $stOrdem Parâmetro de Ordenação
* @param  Object $boTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function recuperaUF(&$rsResultado , $boTransacao = "")
{
    if( $_REQUEST["inCodUF"])
        $stFiltro = " WHERE cod_uf = ".$_REQUEST["inCodUF"];
    $obErro = $this->obTUF->recuperaPorChave( $rsResultado, $boTransacao );

    return $obErro;
}

/**
* Executa um recuperaTodos na classe Persistente Municipios
* @access Public
* @param  Object $rsListaCategoria Retorna o RecordSet preenchido
* @param  String $stOrdem Parâmetro de Ordenação
* @param  Object $boTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function recuperaTodosMunicipio(&$rsResultado , $stFiltro = "", $boTransacao = "")
{
    if ($_REQUEST["inCodUF"]) {
        $stFiltro = " WHERE cod_uf = ".$_REQUEST["inCodUF"];
    } else {
        $stFiltro = " WHERE cod_uf = ". $stFiltro;
    }
    $obErro = $this->obTMunicipio->recuperaTodos( $rsResultado, $stFiltro, "nom_municipio", $boTransacao );

    return $obErro;
}

}

?>