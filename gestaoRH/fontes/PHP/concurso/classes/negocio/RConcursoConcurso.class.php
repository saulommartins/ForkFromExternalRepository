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
* Classe de regra de negócio para Concurso
* Data de Criação: 22/03/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: João Rafael Tissot

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.01.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_CON_MAPEAMENTO   ."TConcursoEdital.class.php"                                 );
include_once ( CAM_GRH_CON_MAPEAMENTO   ."TConcursoConcursoCargo.class.php"                            );
include_once ( CAM_GRH_CON_MAPEAMENTO   ."TConcursoHomologacao.class.php"                              );
include_once ( CAM_GRH_CON_NEGOCIO      ."RConfiguracaoConcurso.class.php"                             );
include_once ( CAM_GA_NORMAS_NEGOCIO    ."RNorma.class.php"                                            );
include_once ( CAM_GRH_PES_NEGOCIO      ."RPessoalCargo.class.php"                                     );
include_once ( CAM_GA_ADM_NEGOCIO       ."RCadastroDinamico.class.php"                                 );
include_once ( CAM_GRH_CON_MAPEAMENTO."TConcursoAtributoConcursoValor.class.php" );

/**
    * Classe de Regra de Negócio Concurso
    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Rafael Almeida
*/
class RConcursoConcurso
{
/**
    * @var Object
    * @access Private
*/
var $obTConcursoConcurso;
/**
    * @var Object
    * @access Private
*/
var $obTConcursoHomologacao;
/**
    * @var Object
    * @access Private
*/
var $obTConfiguracao;
/**
    * @var Object
    * @access Private
*/
var $obTConcursoConcursoCargo;
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var Object
    * @access Private
*/
var $obRNorma;
/**
    * @var Object
    * @access Private
*/
var $obRTipoNorma;
/**
    * @var Object
    * @access Private
*/
var $obRPessoalCargo;
/**
    * @var Object
    * @access Private
*/
var $obRConfiguracaoConcurso;

/**
    * @var Integer
    * @access Private
*/
var $stEdital;
/**
    * @var Integer
    * @access Private
*/
var $stTipoNorma;
/**
    * @var Integer
    * @access Private
*/
var $dtAplicacao;
/**
    * @var String
    * @access Private
*/
var $stNotaMinima;
/**
    * @var Date
    * @access Private
*/
var $dtProrrogacao;
/**
    * @var Integer
    * @access Private
*/
var $inMesesValidade;
/**
    * @var String
    * @access Private
*/
var $stTipoProva;
/**
    * @var Boolean
    * @access Private
*/
var $boAvaliaTitulacao;
/**
     * @access Public
     * @return Object
*/
var $obRCadastroDinamico;
/**
    * @var Date
    * @access Private
*/
var $inCodHomologacao;
/**
    * @var Array
    * @access Private
*/
var $arCargos;
/**
    * @access Public
    * @param Integer $valor
*/
function setTConcursoConcurso($valor) { $this->obTConcursoConcurso = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTConcursoConcursoCargo($valor) { $this->obTConcursoConcursoCargo     = $valor;}
/**
     * @access Public
     * @param Object $valor
*/
function setTConcursoHomologacao($valor) { $this->obTConcursoHomologacao     = $valor;}
/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRNorma($valor) { $this->obRNorma            = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRTipoNorma($valor) { $this->obRTipoNorma            = $valor; }
/**
     * @access Public
     * @param Object $valor
*/

function setRConfiguracaoConcurso($valor) { $this->obRConfiguracaoConcurso            = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setTipoProva($valor) { $this->boTipoProva     = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setAvaliaTitulacao($valor) { $this->boAvaliaTitulacao         = $valor; }
/**
     * @access Public
     * @param Numeric $valor
*/
function setUltimoCargo($valor) { $this->obUltimoCargo       = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setNotaMinima($valor) { $this->nuNotaMinima        = $valor; }
/**
     * @access Public
     * @param Date $valor
*/
function setCargos($valor) { $this->arCargos            = $valor; }
/**
     * @access Public
     * @return Object
*/
function setAplicacao($valor) { $this->dtAplicacaoo        = $valor; }
/**
     * @access Public
     * @return Object
*/
function setCodEditalHomologacao($valor) { $this->inCodHomologacao        = $valor; }
/**
     * @access Public
     * @param Date $valor
*/
function setMesesValidade($valor) { $this->inMesesValidade  =   $valor;}
/**
    * @access Public
    * @param Integer
*/

function setCodEdital($valor) { $this->stEdital         = $valor; }
/**
     * @access Public
     * @param Date $valor
*/
function setProrrogacao($valor) { $this->dtProrrogacao       = $valor; }
/**
     * @access Public
     * @return Object
*/
function setExercicio($valor) { $this->inExercicio       = $valor; }
/**
     * @access Public
     * @return Date $valor
*/
function getTConcursoConcurso() { return $this->obTConcursoConcurso;     }
/**
     * @access Public
     * @return Object
*/
function getRConfiguracaoConcurso() { return $this->obRConfiguracaoConcurso;  }
/**
     * @access Public
     * @return Object
*/
function getRConcursoConcursoCargo() { return $this->obTConcursoConcursoCargo;}
/**
     * @access Public
     * @return Object
*/
function getTransacao() { return $this->obTransacao;     }
/**
     * @access Public
     * @return Object
*/
function getRNorma() { return $this->obRNorma;        }
/**
     * @access Public
     * @return Object
*/
function getRTipoNorma() { return $this->obRTipoNorma;        }
/**
     * @access Public
     * @return Integer
*/
function getMesesValidade() {return $this->inMesesValidade;       }
/**
    * @access Public
    * @return Integer
*/
function getCargos() { return $this->arCargos;        }
/**
    * Método Construtor
    * @access Private
*/
function getUltimoCargo() { return $this->obUltimoCargo;   }
/**
     * @access Public
     * @return Object
*/
function getCodEdital() { return $this->stEdital;        }
/**
     * @access Public
     * @return Integer
*/
function getTipoProva() { return $this->boTipoProva; }
/**
     * @access Public
     * @return Boolean
*/
function getAvaliaTitulacao() { return $this->boAvaliaTitulacao;     }
/**
     * @access Public
     * @return Numeric
*/
function getNotaMinima() { return $this->nuNotaMinima;    }
/**
     * @access Public
     * @return Integer
*/
function getAplicacao() { return $this->dtAplicacaoo;    }
/**
     * @access Public
     * @return Date
*/
function getProrrogacao() { return $this->dtProrrogacao;   }
/**
     * @access Public
     * @return Date
*/
function getCodEditalHomologacao() { return $this->inCodHomologacao;   }
/**
    * Método Construtor
    * @access Private
*/
function getExercicio() { return $this->inExercicio;   }
/**
    * Método Construtor
    * @access Private
*/

function RConcursoConcurso()
{
    $this->setTConcursoConcurso         ( new TConcursoEdital         );
    $this->setTConcursoHomologacao      ( new TConcursoHomologacao      );
    $this->setRConfiguracaoConcurso     ( new RConfiguracaoConcurso     );
    $this->setTConcursoConcursoCargo    ( new TConcursoConcursoCargo    );
    $this->setTransacao                 ( new Transacao                 );
    $this->setRNorma                    ( new RNorma                    );
    $this->setRTipoNorma                ( new RTipoNorma                );
    $this->obRPessoalCargo              = new RPessoalCargo;
    $this->obRCadastroDinamico                          = new RCadastroDinamico;
    $this->obRCadastroDinamico->setPersistenteValores   ( new TConcursoAtributoConcursoValor );
    $this->obRCadastroDinamico->setCodCadastro          ( 1 );
    $this->obRCadastroDinamico->obRModulo->setCodModulo ( 17 );
}

/**
    * Inclui Concurso no Banco de Dados
    * @access Private
    * @return Object Objeto Erro
*/
function setaChave()
{
    $this->obTConcursoConcurso->setDado( "cod_edital"       , $this->getCodEdital() );
    $this->obTConcursoConcurso->setDado( "cod_norma"        , $this->getRNorma() );
    $this->obTConcursoConcurso->setDado( "dt_aplicacao"     , $this->getAplicacao() );
    $this->obTConcursoConcurso->setDado( "dt_prorrogacao"   , $this->getProrrogacao() );
    $this->obTConcursoConcurso->setDado( "nota_minima"      , $this->getNotaMinima() );
    $this->obTConcursoConcurso->setDado( "meses_validade"   , $this->getMesesValidade() );
    $this->obTConcursoConcurso->setDado( "avalia_titulacao" , $this->getAvaliaTitulacao() );
    $this->obTConcursoConcurso->setDado( "tipo_prova"       , $this->getTipoProva() );

    $this->obTConcursoHomologacao->setDado( "cod_homologacao"   , $this->getCodEditalHomologacao() );
    $this->obTConcursoHomologacao->setDado( "cod_edital"        , $this->getCodEdital() );
}

/**
    * Inclui Concurso no Banco de Dados
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirConcurso($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    //insere CONCURSO
    if ( !$obErro->ocorreu() ) {
        $this->setaChave();
        // inclui os dados do concurso
        $obErro = $this->obTConcursoConcurso->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            // salva os valores dos atributos dinâmicos
            $arChaveAtributoCandidato =  array( "cod_edital" => $this->getCodEdital() );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
            $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
        }
    }
    //salva CARGOS do CONCURSO
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->salvarCargos( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConcursoConcurso );

    return $obErro;
}

/**
    * Altera concurso ja incluido no Banco de Dados
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarConcurso($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    //altera CONCURSO
    if ( !$obErro->ocorreu() ) {
        $this->setaChave();
        $obErro = $this->obTConcursoConcurso->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTConcursoHomologacao->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( $this->getCodEditalHomologacao() ) {
                    $obErro = $this->obTConcursoHomologacao->inclusao( $boTransacao );
                }
                if ( !$obErro->ocorreu() ) {
                    // altera os valores dos atributos dinâmicos
                    $arChaveAtributoCandidato =  array( "cod_edital" => $this->getCodEdital() );
                    $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
                    $obErro = $this->obRCadastroDinamico->alterarValores( $boTransacao );
                }
            }
        }
    }
    //salva CARGOS do CONCURSO
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->salvarCargos( $boTransacao );
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConcursoConcurso );

    return $obErro;
}

/**
    * Prorroga a data do concurso
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function prorrogarConcurso($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    //altera CONCURSO
    if ( !$obErro->ocorreu() ) {
            $this->setaChave();
            $obErro = $this->obTConcursoConcurso->alteracao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConcursoConcurso );
    }

    return $obErro;
}

/**
    * Recupera todos os Concursos de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarConcursoPorExercicio(&$rsLista, $stFiltro = "", $stOrder = "", $obTransacao = "")
{
    if ($this->getCodEdital()!="") {
        $stFiltro .= " AND C.cod_edital = ".$this->getCodEdital();
    }
    $obErro = $this->obTConcursoConcurso->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Recupera todos os Concursos homologados de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarConcursoHomologadoPorExercicio(&$rsLista, $stFiltro = "", $stOrder = "", $obTransacao = "")
{
    if ($this->getCodEdital()!="") {
        $stFiltro .= " AND C.cod_edital = ".$this->getCodEdital();
    }
    $obErro = $this->obTConcursoConcurso->recuperaRelacionamentoHomologados( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

function consultarConcurso(&$rsLista, &$rsCargos, $stFiltro = "", $stOrder = "", $obTransacao = "")
{
    if ($this->getCodEdital()!="") {
        $stFiltro .= " AND C.cod_edital = ".$this->getCodEdital();
    }
    $obErro = $this->obTConcursoConcurso->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $obTransacao );
    if (!$obErro->ocorreu()) {
            $obErro = $this->obTConcursoConcursoCargo->listarCargos( $rsCargos, $stFiltro, $stOrder, $obTransacao );
        }

    return $obErro;
}

function consultarConcursoHomologacao(&$rsLista, $stFiltro = "", $stOrder = "", $obTransacao = "")
{
    if ( $this->getCodEdital()!="" ) {
        $stFiltro .= " and ch.cod_edital = ".$this->getCodEdital();
    }
    $obErro = $this->obTConcursoHomologacao->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Recupera os editais da tabela normas.norma
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarEditais($inCodTipoNormaEdital, &$rsLista, $stOrder = "", $obTransacao = "")
{
    if ( !empty($inCodTipoNormaEdital) )
        $stFiltro = " where N.cod_tipo_norma = ".$inCodTipoNormaEdital;
    else
        $stFiltro = " where N.cod_tipo_norma <> 0 ";
    $stOrder = "";
    $obErro = $this->obRNorma->obTNorma->recuperaNormas( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Recupera todos os Concursos não prorrogados de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

/**
    * Instancia um novo objeto do tipo cargos
    * @access Public
*/
function addCargo()
{
    $this->setUltimoCargo( new RPessoalCargo );
}

/**
    * Adiciona o objeto do tipo cargo ao array de cargos
    * @access Public
*/
function commitCargo()
{
    $arCargos   = $this->getCargos();
    $arCargos[] = $this->getUltimoCargo();
    $this->setCargos( $arCargos );
}

/**
    * Instancia um novo objeto do tipo Cargo
    * @access Public
*/

function addPessoalCargo()
{
    $this->arRPessoalCargo[] = new RPessoalCargo( $this );
    $this->roUltimoPessoalCargo = &$this->arRPessoalCargo[ count($this->arRPessoalCargo) - 1 ];
}

function listarConcursoNaoProrrogado(&$rsLista, $stOrder = "", $obTransacao = "")
{
    $stFiltro = "";
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND C.ano_exercicio = ".$this->getExercicio()." AND C.dt_prorrogacao IS NULL";
    }
    $obErro = $this->obTConcursoConcurso->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Salva os Membros da Comissão no banco de dados
    * @access Private
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarCargos($boTransacao = "")
{
    $this->obTConcursoConcursoCargo->setComplementoChave('cod_edital,cod_cargo'   );
    $this->obTConcursoConcursoCargo->setDado( "cod_edital"  , $this->getCodEdital()      );
    $obErro = $this->obTConcursoConcursoCargo->exclusao( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arCargos = $this->getCargos();
        foreach ($arCargos as $obCargos) {
            $this->obTConcursoConcursoCargo->setDado( "cod_cargo" , $obCargos->getCodCargo() );
            $obErro = $this->obTConcursoConcursoCargo->inclusao( $boTransacao );
            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }

    return $obErro;}
/**
    * Recupera exercicio dos CONCURSOS cadastrados
    * @access Public
    * @param  Object $arConfiguracao Retorna o array preenchido
    * @return Object Objeto Erro
*/
function recuperaExercicio(&$rsExercicio, $stFiltro)
{
    $obErro = $this->obTConcursoConcurso->recuperaExercicio( $rsExercicio, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}
/**
    * Recupera notas do concurso para gerar classificacao dos candidatos
    * @access Public
    * @param  Object $arConfiguracao Retorna o array preenchido
    * @return Object Objeto Erro
*/
function recuperaNotasEdital(&$rsNotas)
{
    $stFiltro = " WHERE ccc.cod_edital = ".$this->getCodEdital();
    $stOrder = " GROUP By t.media ORDER BY t.media DESC";
    $obErro = $this->obTConcursoConcurso->recuperaNotasEdital( $rsNotas, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}
/**
    * Recupera a configuracao do modulo CONCURSOS
    * @access Public
    * @param  Object $arConfiguracao Retorna o array preenchido
    * @return Object Objeto Erro
*/
function recuperaConfiguracao(&$arConfiguracao)
{
    $arConfiguracao = array();
    $obErro = $this->obRConfiguracaoConcurso->consultarConfiguracao();
    $arConfiguracao['mascara_nota'] = $this->obRConfiguracaoConcurso->getMascaraNota();
    $arConfiguracao['tipo_portaria_edital'] = $this->obRConfiguracaoConcurso->getTipoPortariaEdital();

    return $obErro;
}
}
