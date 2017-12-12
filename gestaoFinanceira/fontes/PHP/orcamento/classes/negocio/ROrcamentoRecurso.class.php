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
    * Classe de Regra de Negócio Recurso
    * Data de Criação   : 01/04/2004

    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 31583 $
    $Name$
    $Autor: $
    $Date: 2008-01-03 17:57:13 -0200 (Qui, 03 Jan 2008) $

    * Casos de uso: uc-02.01.05
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"      );

/**
    * Classe de Regra de Negócio Recurso
    * Data de Criação   : 01/04/2004
    * @author Diego Barbosa Victoria
*/
class ROrcamentoRecurso
{
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var Integer
    * @access Private
*/
var $inCodRecurso;
/**
    * @var Integer
    * @access Private
*/
var $inCodRecursoContraPartida;
/**
    * @var Integer
    * @access Private
*/
var $inCodRecursoInicial;
/**
    * @var Integer
    * @access Private
*/
var $inCodRecursoFinal;
/**
    * @var Integer
    * @access Private
*/
var $inCodEspecificacaoInicial;
/**
    * @var Integer
    * @access Private
*/
var $inCodEspecificacaoFinal;

/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stNome;
/**
    * @var String
    * @access Private
*/
var $stFinalidade;
/**
    * @var String
    * @access Private
*/
var $stTipo;
/**
    * @var String
    * @access Private
*/
var $stTipoEsfera;
/**
    * @var String
    * @access Private
*/
var $stNomeTipo;
/**
    * @var Integer
    * @access Private
*/
var $inCodFonteRecurso;
/**
    * @var String
    * @access Private
*/
var $stNomFonteRecurso;
/**
    * @var Integer
    * @access Private
*/
var $inCodigoTC;
var $stMascRecurso;
var $stDestinacaoRecurso;
var $inCodDetalhamento;
var $getCodRecurso;

/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao              = $valor; }
/**
     * @access Public 
     * @param Integer $valor
*/
function setCodRecurso($valor) { $this->inCodRecurso         = $valor; }
/**
     * @access Public 
     * @param Integer $valor
*/
function setCodRecursoContraPartida($valor) { $this->inCodRecursoContraPartida = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodRecursoInicial($valor) { $this->inCodRecursoInicial = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodRecursoFinal($valor) { $this->inCodRecursoFinal   = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodEspecificacaoInicial($valor) { $this->inCodEspecificacaoInicial = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodEspecificacaoFinal($valor) { $this->inCodEspecificacaoFinal   = $valor; }

/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio          = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setNome($valor) { $this->stNome               = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setFinalidade($valor) { $this->stFinalidade         = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setTipo($valor) { $this->stTipo                = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setTipoEsfera($valor) { $this->stTipoEsfera    = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setNomeTipo($valor) { $this->stNomeTipo                = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodFonteRecurso($valor) { $this->inCodFonteRecurso         = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setNomFonteRecurso($valor) { $this->stNomFonteRecurso         = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodigoTC($valor) { $this->inCodigoTC         = $valor; }
function setDestinacaoRecurso($valor) { $this->stDestinacaoRecurso = $valor; }
function setCodDetalhamento($valor) { $this->inCodDetalhamento = $valor; }
function setMascRecurso($valor) { $this->stMascRecurso = $valor; }

/**
     * @access Public
     * @return Object
*/
function getTransacao() { return $this->obTransacao;         }
/**
     * @access Public
     * @return Integer
*/
function getCodRecurso() { return $this->inCodRecurso;        }
/**
     * @access Public
     * @return Integer
*/
function getCodRecursoContraPartida() { return $this->inCodRecursoContraPartida;        }
/**
     * @access Public
     * @return Integer
*/
function getCodRecursoInicial() { return $this->inCodRecursoInicial;}
/**
     * @access Public
     * @return Integer
*/
function getCodRecursoFinal() { return $this->inCodRecursoFinal;  }
/**
     * @access Public
     * @return Integer
*/
function getCodEspecificacaoInicial() { return $this->inCodEspecificacaoInicial;}
/**
     * @access Public
     * @return Integer
*/
function getCodEspecificacaoFinal() { return $this->inCodEspecificacaoFinal;  }

/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;         }
/**
     * @access Public
     * @return String
*/
function getNome() { return $this->stNome;              }
/**
     * @access Public
     * @return String
*/
function getFinalidade() { return $this->stFinalidade;        }
/**
     * @access Public
     * @return String
*/
function getTipo() { return $this->stTipo;        }
/**
     * @access Public
     * @return String
*/
function getTipoEsfera() { return $this->stTipoEsfera;        }
/**
     * @access Public
     * @return String
*/
function getNomeTipo() { return $this->stNomeTipo;        }
/**
     * @access Public
     * @return Integer
*/
function getCodFonteRecurso() { return $this->inCodFonteRecurso;        }
/**
     * @access Public
     * @return String
*/
function getNomFonteRecurso() { return $this->stNomFonteRecurso;         }
/**
     * @access Public
     * @return Integer
*/
function getCodigoTC() { return $this->inCodigoTC;        }
function getMascRecurso() { return $this->stMascRecurso;     }
function getDestinacaoRecurso() { return $this->stDestinacaoRecurso; }
function getCodDetalhamento() { return $this->inCodDetalhamento; }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRecurso()
{
    $this->setTransacao        ( new Transacao         );
    $this->setExercicio        ( Sessao::getExercicio()    );
}

/**
    * Salva o RECURSO dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDireto.class.php" );
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php" );
    $obTOrcamentoRecursoDireto = new TOrcamentoRecursoDireto;
    $obTOrcamentoRecurso       = new TOrcamentoRecurso;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
            $stFiltro = " WHERE cod_recurso = " . $this->getCodRecurso();
            $stFiltro.= " AND exercicio = '" . $this->getExercicio() . "' ";
            $obErro = $obTOrcamentoRecurso->recuperaTodos($rsRecurso, $stFiltro,'',$boTransacao);
            if ( !$obErro->ocorreu() ) {
                if ( $rsRecurso->eof() ) {
                    $obTOrcamentoRecurso->setDado("cod_recurso" , $this->getCodRecurso() );
                    $obTOrcamentoRecurso->setDado("cod_fonte"   , $this->getCodRecurso() );
                    $obTOrcamentoRecurso->setDado("exercicio"   , $this->getExercicio()  );
                    $obErro = $obTOrcamentoRecurso->inclusao( $boTransacao );
                    if (!$obErro->ocorreu()) {
                        $obTOrcamentoRecursoDireto->setDado( "cod_recurso" , $this->getCodRecurso()      );
                        $obTOrcamentoRecursoDireto->setDado( "exercicio"   , $this->getExercicio()       );
                        $obTOrcamentoRecursoDireto->setDado( "nom_recurso" , $this->getNome()            );
                           $obTOrcamentoRecursoDireto->setDado( "finalidade"  , $this->getFinalidade()      );
                        $obTOrcamentoRecursoDireto->setDado( "tipo"        , $this->getTipo()            );
                        $obTOrcamentoRecursoDireto->setDado( "cod_fonte"   , $this->getCodFonteRecurso() );
                        $obTOrcamentoRecursoDireto->setDado( "codigo_tc"   , $this->getCodigoTC()        );
                        $obTOrcamentoRecursoDireto->setDado( "cod_tipo_esfera" , $this->getTipoEsfera() );
                        $obErro = $obTOrcamentoRecursoDireto->inclusao( $boTransacao );
                    } else {
                        $obErro->setDescricao("Código ".$this->getCodRecurso()." já cadastrado!");
                    }
                } else {
                    $obErro->setDescricao( "Já existe um recurso com o código informado (".$this->getCodRecurso().").");
                }

                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoRecursoDireto );
            }
   }

    return $obErro;
}

/**
    * Altera o RECURSO dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDireto.class.php" );
    $obTOrcamentoRecursoDireto = new TOrcamentoRecursoDireto;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoRecursoDireto->setDado( "cod_recurso" , $this->getCodRecurso()  );
        $obTOrcamentoRecursoDireto->setDado( "exercicio"   , $this->getExercicio()   );
        $obTOrcamentoRecursoDireto->setDado( "nom_recurso" , $this->getNome()        );
        $obTOrcamentoRecursoDireto->setDado( "finalidade"  , $this->getFinalidade()  );
        $obTOrcamentoRecursoDireto->setDado( "tipo"        , $this->getTipo()        );
        $obTOrcamentoRecursoDireto->setDado( "cod_fonte"   , $this->getCodFonteRecurso() );
        $obTOrcamentoRecursoDireto->setDado( "codigo_tc"   , $this->getCodigoTC()        );
        $obTOrcamentoRecursoDireto->setDado( "cod_tipo_esfera" , $this->getTipoEsfera() );
        $obErro = $obTOrcamentoRecursoDireto->alteracao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoRecursoDireto );
    }

    return $obErro;
}

/**
    * Exclui os dados do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDireto.class.php"   );
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php"         );
    include_once ( CAM_GF_PPA_MAPEAMENTO."TPPAAcaoRecurso.class.php"           );
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php");

    $obTOrcamentoRecursoDireto     = new TOrcamentoRecursoDireto;
    $obTOrcamentoRecurso           = new TOrcamentoRecurso;
    $obTPPAAcaoRecurso             = new TPPAAcaoRecurso;
    $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obTPPAAcaoRecurso->recuperaTodos($rsPPAAcaoRecurso, " WHERE cod_recurso = ".$this->getCodRecurso()." AND exercicio_recurso = '".$this->getExercicio()."'", "",$boTransacao);

        $obTOrcamentoRecursoDestinacao->setDado  ('cod_recurso', $this->getCodRecurso() );
        $obTOrcamentoRecursoDestinacao->setDado  ('exercicio'  , $this->getExercicio()  );
        $obErro = $obTOrcamentoRecursoDestinacao->recuperaPorChave($rsRecursoDestinacao, $boTransacao);

        if ( !$obErro->ocorreu() ) {
            if ($rsPPAAcaoRecurso->getNumlinhas() > 0 || $rsRecursoDestinacao->getNumlinhas() > 0) {
                $obErro->setDescricao('Recurso não pode ser excluído porque está sendo utilizado.');
            }else{
                $obTOrcamentoRecursoDireto->setDado( "cod_recurso" , $this->getCodRecurso()      );
                $obTOrcamentoRecursoDireto->setDado( "exercicio"   , $this->getExercicio()       );
                $obTOrcamentoRecursoDireto->recuperaPorChave($rsTOrcamentoRecursoDireto, $boTransacao);
                $obErro = $obTOrcamentoRecursoDireto->exclusao( $boTransacao );    

                if (!$obErro->ocorreu()) {
                    if(SistemaLegado::pegaConfiguracao('cod_uf') == '16' ){
                        include_once ( TTPB."TTPBRecurso.class.php" );
                        include_once ( CAM_GF_ORC_MAPEAMENTO."TTCEPECodigoFonteRecurso.class.php" );

                        $obTTPBRecurso = new TTPBRecurso();
                        $obTTPBRecurso->setDado  ('cod_recurso', $this->getCodRecurso() );
                        $obTTPBRecurso->setDado  ('exercicio'  , $this->getExercicio()  );
                        $obErro = $obTTPBRecurso->exclusao ( $boTransacao );

                        if (!$obErro->ocorreu()) {
                            $obTTCEPECodigoFonteRecurso = new TTCEPECodigoFonteRecurso();
                            $obTTCEPECodigoFonteRecurso->setDado  ( 'cod_recurso', $this->getCodRecurso() );
                            $obTTCEPECodigoFonteRecurso->setDado  ( 'exercicio'  , $this->getExercicio()  );
                            $obErro = $obTTCEPECodigoFonteRecurso->exclusao ( $boTransacao );
                        }
                    }

                    $obTOrcamentoRecurso->setDado( "cod_recurso" , $this->getCodRecurso() );
                    $obTOrcamentoRecurso->setDado( "exercicio"   , $this->getExercicio()     );
                    $obErro = $obTOrcamentoRecurso->exclusao( $boTransacao );
                }
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoRecurso );
            }
        }
    }

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente Recurso
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php" );
    $obTOrcamentoRecurso = new TOrcamentoRecurso;

    $stFiltro = "";
    if ( $this->getCodRecurso() != "" OR $this->getCodRecurso =="0") {
        $obTOrcamentoRecurso->setDado('cod_recurso', $this->getCodRecurso());
        //$stFiltro .= " cod_recurso = ".$this->getCodRecurso()." AND";
    }
    //if ( $this->getCodRecurso() == 0) {
    //    $stFiltro .= " cod_recurso = 0 AND";
    // }
    if ( $this->getExercicio() ) {
        $obTOrcamentoRecurso->setDado('exercicio', $this->getExercicio());
        //$stFiltro .= " exercicio   = '".$this->getExercicio()."' AND";
    }
    if( $this->getTipo() )
        $stFiltro .= " tipo = '".$this->getTipo()."' AND";

    if ( $this->getNome() ) {
        $stFiltro .= " lower(nom_recurso)  like lower('%".$this->getNome()."%') AND";
    }
    $obTOrcamentoRecurso->setDado("exercicio" , $this->getExercicio()  );

    if ($stFiltro != '') $stFiltro = " AND ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    $stOrder = ($stOrder) ? $stOrder : "cod_recurso";
    $obErro = $obTOrcamentoRecurso->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarRecursoDireto(&$rsLista, $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDireto.class.php" );
    $obTOrcamentoRecursoDireto = new TOrcamentoRecursoDireto;
    $stFiltro = "";
    if ( $this->getCodRecurso() != "" OR $this->getCodRecurso =="0") {
        $stFiltro .= " cod_recurso = ".$this->getCodRecurso()." AND";
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " exercicio   = '".$this->getExercicio()."' AND";
    }
    if( $this->getTipo() )
        $stFiltro .= " tipo   = '".$this->getTipo()."' AND";

    if ( $this->getNome() ) {
        $stFiltro .= " lower(nom_recurso)  like lower('%".$this->getNome()."%') AND";
    }
    $obTOrcamentoRecursoDireto->setDado("exercicio" , $this->getExercicio()  );

    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    $stOrder = ($stOrder) ? $stOrder : "cod_recurso";
    $obErro = $obTOrcamentoRecursoDireto->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente Recurso
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar(&$rsLista, $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php" );
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDireto.class.php" );
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoFonteRecurso.class.php" );
    include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );

    $obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
    $obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
    $stRecursoDestinacao = $obRConfiguracaoOrcamento->consultarConfiguracaoEspecifica( 'recurso_destinacao', $boTransacao );
    if ($stRecursoDestinacao == 'true') {
        $obTOrcamentoRecurso = new TOrcamentoRecurso;
        $obTOrcamentoRecurso->setDado('cod_recurso', $this->getCodRecurso() );
        $obTOrcamentoRecurso->setDado('exercicio', $this->getExercicio() );
        $obErro = $obTOrcamentoRecurso->recuperaRelacionamento( $rsLista, '', '', $boTransacao );
        if (!$obErro->ocorreu()) {
            $this->stNome  = $rsLista->getCampo("nom_recurso");
            $this->stMascRecurso = $rsLista->getCampo("masc_recurso");
            $this->inCodFonteRecurso = $rsLista->getCampo("cod_fonte");
        }

    } else {
        $obTOrcamentoRecursoDireto = new TOrcamentoRecursoDireto;
        $obTOrcamentoFonteRecurso = new TOrcamentoFonteRecurso;

        $obTOrcamentoRecursoDireto->setDado( "cod_recurso"   , $this->getCodRecurso () );
        $obTOrcamentoRecursoDireto->setDado( "exercicio"     , $this->getExercicio  () );
        if ( $this->getCodRecurso() ) {
            $obErro = $obTOrcamentoRecursoDireto->recuperaRelacionamento( $rsLista, 'WHERE cod_recurso = '. $this->getCodRecurso(), '', $boTransacao );
        } else {
            $obErro = $obTOrcamentoRecursoDireto->recuperaRelacionamento( $rsLista, '', '', $boTransacao );
        }

        if ( !$obErro->ocorreu() ) {
            $this->stNome            =  $rsLista->getCampo( "nom_recurso" );
            $this->stMascRecurso     =  $rsLista->getCampo("masc_recurso");
            $this->stFinalidade      =  $rsLista->getCampo( "finalidade"  );
            $this->stTipo            =  $rsLista->getCampo( "tipo"        );
            $this->inCodFonteRecurso =  $rsLista->getCampo( "cod_fonte"   );
            $this->inCodigoTC        =  $rsLista->getCampo( "codigo_tc"   );
            $this->stTipoEsfera      =  $rsLista->getCampo( "cod_tipo_esfera" );
            if($rsLista->getCampo( "tipo")=="V")
                $this->stNomeTipo       =  "Vinculado";
            else
                $this->stNomeTipo       =  "Livre";

            $obTOrcamentoFonteRecurso->setDado( "cod_fonte"   , $this->getCodFonteRecurso() );
            $obErro = $obTOrcamentoFonteRecurso->recuperaPorChave( $rsFonteRecurso, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->stNomFonteRecurso = $rsFonteRecurso->getCampo("descricao");
            }
        }
    }

    return $obErro;
}

/*
    Utilizado somente nas ações da rotina Orçamento :: Recurso
*/
function consultarRecursoDireto(&$rsLista, $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDireto.class.php" );
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoFonteRecurso.class.php" );
    $obTOrcamentoRecursoDireto = new TOrcamentoRecursoDireto;
    $obTOrcamentoFonteRecurso = new TOrcamentoFonteRecurso;

    $obTOrcamentoRecursoDireto->setDado( "cod_recurso"   , $this->getCodRecurso () );
    $obTOrcamentoRecursoDireto->setDado( "exercicio"     , $this->getExercicio  () );
    $obErro = $obTOrcamentoRecursoDireto->recuperaPorChave( $rsLista, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stNome            =  $rsLista->getCampo( "nom_recurso" );
        $this->stFinalidade      =  $rsLista->getCampo( "finalidade"  );
        $this->stTipo            =  $rsLista->getCampo( "tipo"        );
        $this->inCodFonteRecurso =  $rsLista->getCampo( "cod_fonte"   );
        $this->inCodigoTC        =  $rsLista->getCampo( "codigo_tc"   );
        $this->stTipoEsfera      =  $rsLista->getCampo( "cod_tipo_esfera" );
        if($rsLista->getCampo( "tipo")=="V")
            $this->stNomeTipo       =  "Vinculado";
        else
            $this->stNomeTipo       =  "Livre";

        $obTOrcamentoFonteRecurso->setDado( "cod_fonte"   , $this->getCodFonteRecurso() );
        $obErro = $obTOrcamentoFonteRecurso->recuperaPorChave( $rsFonteRecurso, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->stNomFonteRecurso = $rsFonteRecurso->getCampo("descricao");
        }
    }

    return $obErro;
}

/**
    * Executa um pegaConfiguracao na classe TConfiguracao
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaMascaraRecurso(&$stValor, $boTransacao = "")
{
    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
    $obTAdministracaoConfiguracao    = new TAdministracaoConfiguracao;

    $obTAdministracaoConfiguracao->setDado( "cod_modulo", 8);
    $obTAdministracaoConfiguracao->setDado( "exercicio", $this->stExercicio );
    $obTAdministracaoConfiguracao->setDado( "parametro", "masc_recurso" );
    $obErro = $obTAdministracaoConfiguracao->pegaConfiguracao( $stValor, $boTransacao );

    return $obErro;
}

/**
    * Verifica se o recurso já está sendo utilizado em algum empenho do exercicio informado
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaUtilizacao(/* &$rsLista,*/)
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php" );
    $obTOrcamentoRecurso = new TOrcamentoRecurso;

    $obTOrcamentoRecurso->setDado( "cod_recurso"   , $this->getCodRecurso () );
    $obTOrcamentoRecurso->setDado( "exercicio"     , $this->getExercicio  () );
    $obErro = $obTOrcamentoRecurso->verificaUtilizacao( $rsLista, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ($rsLista->getNumlinhas() == 1) return true;
        else return false;
    } else return $obErro;
}

function listarRecursoSemConta(&$rsLista, $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php" );
    $obTOrcamentoRecurso = new TOrcamentoRecurso;

    if ($this->getCodRecursoInicial() != "") {
        $obTOrcamentoRecurso->setDado('cod_recurso_inicial', $this->getCodRecursoInicial());
    }
    if ($this->getCodRecursoFinal() != "") {
        $obTOrcamentoRecurso->setDado('cod_recurso_final', $this->getCodRecursoFinal());
    }
    $obTOrcamentoRecurso->setDado('exercicio', $this->getExercicio());

    $obErro = $obTOrcamentoRecurso->recuperaRecursoSemConta($rsLista, $boTransacao);

    return $obErro;
}

function listarRecursoEspecificacoesSemConta(&$rsLista, $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php" );
    $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;

    if ($this->getCodEspecificacaoInicial() != "") {
        $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao_inicial', $this->getCodEspecificacaoInicial());
    }
    if ($this->getCodEspecificacaoFinal() != "") {
        $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao_final', $this->getCodEspecificacaoFinal());
    }
    $obTOrcamentoRecursoDestinacao->setDado('exercicio', $this->getExercicio());

    $obErro = $obTOrcamentoRecursoDestinacao->recuperaRecursoEspecificacaoSemConta($rsLista, $boTransacao);

    return $obErro;
}

}
