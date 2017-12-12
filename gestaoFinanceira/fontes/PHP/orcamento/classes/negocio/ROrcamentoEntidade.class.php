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
    * Classe de Regra de Negócio Entidade Orçamento
    * Data de Criação   : 15/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @package URBEM
    * @subpackage Regra

    $Id: ROrcamentoEntidade.class.php 65191 2016-04-29 20:02:55Z franver $

    $Revision: 30824 $
    $Name$
    $Author: tonismar $
    $Date: 2008-03-28 10:13:57 -0300 (Sex, 28 Mar 2008) $

    * Casos de uso: uc-02.01.02,uc-02.01.23,uc-02.03.03,ucd-02.04.04,ucd-02.04.19,ucd-02.04.20
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"               );
include_once ( CAM_GA_ADM_NEGOCIO."RUsuario.class.php"                        );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                            );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php"              );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"                );

/**
    * Classe de Regra de Negócio Entidade Orçamento
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Roberto Pawelski Rodrigues
*/
class ROrcamentoEntidade
{
/**
    * @var Object
    * @access Private
*/
var $obRUsuario;
/**
    * @var Object
    * @access Private
*/
var $obRResponsavelTecnico;
/**
    * @var Object
    * @access Private
*/
var $obRCGMPessoaFisica;
/**
    * @var Object
    * @access Private
*/
var $obRCGMPessoaJuridica;

/**
    * @var Object
    * @access Private
*/
var $obRCGM;
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
var $inCodigoEntidade;
/**
    * @var Integer
    * @access Private
*/
var $inNumCGM;
/**
    * @var String
    * @access Private
*/
var $stNomCGM;
/**
    * @var Integer
    * @access Private
*/
var $inConfiguracao;
/**
    * @var Integer
    * @access Private
*/
var $inCodigoResponsavel;
/**
    * @var Integer
    * @access Private
*/
var $inCodigoResponsavelTecnico;
/**
    * @var Integer
    * @access Private
*/
var $inCodigoProfissao;
/**
    * @access Private
    * @var Array
*/
var $arUsuarios;
/**
    * @access Private
    * @var String
*/
var $stNomeEntidade;
/**
    * @access Private
    * @var String
*/
var $stNomeResponsavel;
/**
    * @access Private
    * @var String
*/
var $stNomeResponsavelTecnico;
/**
    * @access Private
    * @var String
*/
var $stArquivoLogotipo;

/**
     * @access Public
     * @param Object $valor
*/
function setNomeEntidade($valor) { $this->stNomeEntidade             = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setNomeResponsavel($valor) { $this->stNomeResponsavel          = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setNomeResponsavelTecnico($valor) { $this->stNomeResponsavelTecnico   = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRUsuario($valor) { $this->obRUsuario                 = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setUltimoUsuario($valor) { $this->obUltimoUsuario            = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRCGMPessoaFisica($valor) { $this->obRCGMPessoaFisica         = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRCGMPessoaJuridica($valor) { $this->obRCGMPessoaJuridica       = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setRCGM($valor) { $this->obRCGM                     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio                = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodigoEntidade($valor) { $this->inCodigoEntidade           = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setNumCGM($valor) { $this->inNumCGM                   = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setNomCGM($valor) { $this->stNomCGM                   = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setVerificaConfiguracao($valor) { $this->inConfiguracao                   = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodigoResponsavel($valor) { $this->inCodigoResponsavel        = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodigoResponsavelTecnico($valor) { $this->inCodigoResponsavelTecnico = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodigoProfissao($valor) { $this->inCodigoProfissao          = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setUsuarios($valor) { $this->arUsuarios                 = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setArquivoLogotipo($valor) { $this->stArquivoLogotipo          = $valor; }

/**
     * @access Public
     * @return Object
*/
function getNomeEntidade() { return $this->stNomeEntidade;                  }
/**
     * @access Public
     * @return Object
*/
function getNomeResponsavel() { return $this->stNomeResponsavel;               }
/**
     * @access Public
     * @return Object
*/
function getNomeResponsavelTecnico() { return $this->stNomeResponsavelTecnico;      }
/**
     * @access Public
     * @return Object
*/
function getRUsuario() { return $this->obRUsuario;                       }
/**
     * @access Public
     * @return Object
*/
function getUltimoUsuario() { return $this->obUltimoUsuario;                  }
/**
     * @access Public
     * @return Object
*/
function getRCGMPessoaFisica() { return $this->obRCGMPessoaFisica;               }
/**
     * @access Public
     * @return Object
*/
function getRCGMPessoaJuridica() { return $this->obRCGMPessoaJuridica;              }
/**
     * @access Public
     * @return Object
*/
function getRCGM() { return $this->obRCGM;                           }
/**
     * @access Public
     * @param Integer $valor
*/
function getVerificaConfiguracao() { return $this->inConfiguracao;                           }
/**
     * @access Public
     * @return Object
*/
function getTransacao() { return $this->obTransacao;                      }
/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;                      }
/**
     * @access Public
     * @return Integer
*/
function getCodigoEntidade() { return $this->inCodigoEntidade;                 }
/**
     * @access Public
     * @return Integer
*/
function getNumCGM() { return $this->inNumCGM;                         }
/**
     * @access Public
     * @return String
*/
function getNomCGM() { return $this->stNomCGM;                         }
/**
     * @access Public
     * @return Integer
*/
function getCodigoResponsavel() { return $this->inCodigoResponsavel;              }
/**
     * @access Public
     * @return Integer
*/
function getCodigoResponsavelTecnico() { return $this->inCodigoResponsavelTecnico;  }
/**
     * @access Public
     * @return Integer
*/
function getCodigoProfissao() { return $this->inCodigoProfissao;                }
/**
     * @access Public
     * @return Array
*/
function getUsuarios() { return $this->arUsuarios;                       }
/**
    * @access Public
    * @return String
*/
function getArquivoLogotipo() { return $this->stArquivoLogotipo;                  }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoEntidade()
{
    $this->setRUsuario              ( new RUsuario                  );
    $this->setRCGM                  ( new RCGM                      );
    $this->setRCGMPessoaFisica      ( new RCGMPessoaFisica          );
    $this->setRCGMPessoaJuridica    ( new RCGMPessoaJuridica        );
    $this->setTransacao             ( new Transacao                 );
    $this->setExercicio             ( Sessao::getExercicio()            );
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"         );
    $obTEntidade             = new TOrcamentoEntidade;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTEntidade->setDado( "cod_entidade"     , $this->getCodigoEntidade()            );
        $obTEntidade->setDado( "numcgm"           , $this->getNumCGM()                    );
        $obTEntidade->setDado( "exercicio"        , $this->getExercicio()                 );
        $obTEntidade->setDado( "cod_responsavel"  , $this->getCodigoResponsavel()         );
        $obTEntidade->setDado( "cod_resp_tecnico" , $this->getCodigoResponsavelTecnico()  );
        $obTEntidade->setDado( "cod_profissao"    , $this->getCodigoProfissao()           );
        $obErro = $obTEntidade->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->salvarUsuarios( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->salvarLogotipo( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEntidade );

    return $obErro;
}

/**
    * Altera os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"         );
    $obTEntidade             = new TOrcamentoEntidade;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTEntidade->setDado( "cod_entidade"     , $this->getCodigoEntidade()            );
        $obTEntidade->setDado( "numcgm"           , $this->getNumCGM()                    );
        $obTEntidade->setDado( "exercicio"        , $this->getExercicio()                 );
        $obTEntidade->setDado( "cod_responsavel"  , $this->getCodigoResponsavel()         );
        $obTEntidade->setDado( "cod_resp_tecnico" , $this->getCodigoResponsavelTecnico()  );
        $obTEntidade->setDado( "cod_profissao"    , $this->getCodigoProfissao()           );
        $obErro = $obTEntidade->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() and $this->getArquivoLogotipo() ) {
            if ( $this->getArquivoLogotipo() === true ) {
                $this->setArquivoLogotipo( '' );
            }
            $obErro = $this->salvarLogotipo( $boTransacao );
        }
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->salvarUsuarios( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEntidade );

    return $obErro;
}

/**
    * Faz a consulta e retorna o próximo código
    * @access Public
*/
function pegarProximoCodigo($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"         );
    $obTEntidade             = new TOrcamentoEntidade;

    $obTEntidade->proximoCod( $inCodigoEntidade , $boTransacao );
    $this->setCodigoEntidade( $inCodigoEntidade );
}

/**
    * Exclui os dados do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"         );
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoUsuarioEntidade.class.php"  );
    $obTUsuarioEntidade      = new TOrcamentoUsuarioEntidade;
    $obTEntidade             = new TOrcamentoEntidade;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->salvarLogotipo( $boTransacao );
        if ( !$obErro->ocorreu() ) {
           $obTUsuarioEntidade->setComplementoChave('cod_entidade');
           $obTUsuarioEntidade->setDado( "cod_entidade", $this->getCodigoEntidade() );
           $obErro = $obTUsuarioEntidade->exclusao( $boTransacao );
           if ( !$obErro->ocorreu() ) {
               $obTEntidade->setDado( "cod_entidade"  , $this->getCodigoEntidade()  );
               $obTEntidade->setDado( "exercicio"     , $this->getExercicio()       );
               $obErro = $obTEntidade->exclusao( $boTransacao );
           }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEntidade );
    }

    return $obErro;
}

/**
    * Executa um recuperaRelacionamento na classe Persistente Entidade
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "", $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"         );
    $obTEntidade             = new TOrcamentoEntidade;

    $stFiltro = "";
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->getCodigoEntidade() ) {
        $stFiltro .= " AND E.cod_entidade = ".$this->getCodigoEntidade();
    }
    if ( $this->getNomCGM() ) {
        $stFiltro .= " AND lower (CGM.nom_cgm) like lower( '%".$this->getNomCGM()."%' ) ";
    }
    if ( $this->getNumCGM() ) {
        $stFiltro .= " AND E.numcgm = ".$this->getNumCGM();
    }
    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= " AND E.numcgm = ".$this->obRCGM->getNumCGM();
    }
    if ( $this->obRCGMPessoaFisica->getNumCGM()) {
        $stFiltro .= " AND E.cod_responsavel = ".$this->obRCGMPessoaFisica->getNumCGM();
    }
    $obErro = $obTEntidade->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;

}

/**
    * Executa um recuperaPorChave na classe Persistente Entidade
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar(&$rsLista, $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"         );
    $obTEntidade             = new TOrcamentoEntidade;

    $obTEntidade->setDado( "exercicio"    , $this->getExercicio()      );
    $obTEntidade->setDado( "cod_entidade" , $this->getCodigoEntidade() );
    $obErro = $obTEntidade->recuperaPorChave( $rsLista, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setExercicio                 ( $rsLista->getCampo( "exercicio" )        );
        $this->setCodigoEntidade            ( $rsLista->getCampo( "cod_entidade" )     );
        $this->setCodigoResponsavel         ( $rsLista->getCampo( "cod_responsavel" )  );
        $this->setCodigoResponsavelTecnico  ( $rsLista->getCampo( "cod_resp_tecnico" ) );
        $this->setCodigoProfissao           ( $rsLista->getCampo( "cod_profissao" )    );
        $this->setNumCGM                    ( $rsLista->getCampo( "numcgm" )           );
        $this->obRCGM->setNumCGM            ( $rsLista->getCampo( "numcgm" )           );
        $obErro = $this->obRCGM->consultar  ( $rs, $boTransacao );
    }

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente Entidade
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarNomes(&$rsLista, $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"         );
    $obTEntidade             = new TOrcamentoEntidade;

    $obTEntidade->setDado( "exercicio"    , $this->getExercicio()      );
    $obTEntidade->setDado( "cod_entidade" , $this->getCodigoEntidade() );
    $obErro = $obTEntidade->recuperaRelacionamentoNomes( $rsLista, '', '', $obTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setExercicio                 ( $rsLista->getCampo( "exercicio" )        );
        $this->setNumCGM                    ( $rsLista->getCampo( "numcgm" )           );
        $this->setNomeEntidade              ( $rsLista->getCampo( "entidade" )         );
        $this->setCodigoResponsavel         ( $rsLista->getCampo( "cod_responsavel" )  );
        $this->setNomeResponsavel           ( $rsLista->getCampo( "responsavel" )      );
        $this->setCodigoResponsavelTecnico  ( $rsLista->getCampo( "cod_resp_tecnico" ) );
        $this->setNomeResponsavelTecnico    ( $rsLista->getCampo( "resp_tecnico" )     );
        $this->setCodigoProfissao           ( $rsLista->getCampo( "cod_profissao" )    );
        $this->setArquivoLogotipo           ( $rsLista->getCampo( "logotipo" )         );
    }

    return $obErro;
}

/**
    * Instancia um novo objeto do tipo usuarios
    * @access Public
*/
function addUsuario()
{
    $this->setUltimoUsuario( new RCGM );
}

/**
    * Adiciona o objeto do tipo usuario ao array de usuarios
    * @access Public
*/
function commitUsuario()
{
    $arUsuarios   = $this->getUsuarios();
    $arUsuarios[] = $this->getUltimoUsuario();
    $this->setUsuarios( $arUsuarios );
}

/**
    * Salva os Membros da Comissão no banco de dados
    * @access Private
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarUsuarios($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoUsuarioEntidade.class.php"  );
    $obTUsuarioEntidade      = new TOrcamentoUsuarioEntidade;

    $obTUsuarioEntidade->setComplementoChave('cod_entidade,exercicio');
    $obTUsuarioEntidade->setDado( "cod_entidade", $this->getCodigoEntidade() );
    $obTUsuarioEntidade->setDado( "exercicio", $this->stExercicio );
    $obErro = $obTUsuarioEntidade->exclusao( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arUsuarios = $this->getUsuarios();
        foreach ($arUsuarios as $obUsuarios) {
            $obTUsuarioEntidade->setDado( "exercicio"    , $this->getExercicio()      );
            $obTUsuarioEntidade->setDado( "numcgm"       , $obUsuarios->getNumCGM()   );
            $obTUsuarioEntidade->setDado( "cod_entidade" , $this->getCodigoEntidade() );
            $obErro = $obTUsuarioEntidade->inclusao( $boTransacao );
            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }

    return $obErro;
}

/**
    * Salva o arquivo de logotipo da entidade
    * @param  Object $boTransacao Parâmetro Transação
*/
function salvarLogotipo($boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidadeLogotipo.class.php" );
    $obTOrcamentoEntidadeLogotipo = new TOrcamentoEntidadeLogotipo;
    $obTOrcamentoEntidadeLogotipo->setDado( "exercicio",    $this->getExercicio() );
    $obTOrcamentoEntidadeLogotipo->setDado( "cod_entidade", $this->getCodigoEntidade() );
    $obErro = $obTOrcamentoEntidadeLogotipo->recuperaPorChave( $rsLogotipo, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stCaminhoAnexo = CAM_GF_ORCAMENTO.'anexos/';
        $stCaminhoTmp   = CAM_GF_ORCAMENTO.'tmp/';
        if ( !$rsLogotipo->EOF() ) {
            $obErro = $obTOrcamentoEntidadeLogotipo->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( is_file( $stCaminhoAnexo.$rsLogotipo->getCampo('logotipo') ) ) {
                    $boErro = unlink( $stCaminhoAnexo.$rsLogotipo->getCampo('logotipo') );
                    if (!$boErro) {
                        $obErro->setDescricao( "Erro excluindo imagem do logotipo." );
                    }
                }
            }
        }
        if ( !$obErro->ocorreu() and $this->getArquivoLogotipo() ) {
            $obTOrcamentoEntidadeLogotipo->setDado( 'logotipo', $this->getArquivoLogotipo() );
            $obErro = $obTOrcamentoEntidadeLogotipo->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( is_file( $stCaminhoTmp.$this->getArquivoLogotipo() ) ) {
                    if ( is_writeable( $stCaminhoAnexo ) ) {
                        $boErro = copy( $stCaminhoTmp.$this->getArquivoLogotipo(), $stCaminhoAnexo.$this->getArquivoLogotipo() );
                        if ($boErro) {
                            $boErro = unlink( $stCaminhoTmp.$this->getArquivoLogotipo() );
                        } else {
                            $obErro->setDescricao("Erro ao tentar copiar o arquivo temporario.(Erro no momento da cópia)");
                        }
                    } else {
                        $obErro->setDescricao( "O diretório ".$stCaminhoAnexo." está sem permissão de escrita." );
                    }
                } else {
                    $obErro->setDescricao( "Erro ao tentar copiar o arquivo temporario.(Arquivo não localizado)." );
                }
            }
        }
    }

    return $obErro;
}

/**
    * Lista membros disponiveis
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarMembrosDisponiveis(&$rsRecordSet, $stOrdem = "", $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoUsuarioEntidade.class.php"  );
    $obTUsuarioEntidade      = new TOrcamentoUsuarioEntidade;

    $obTUsuarioEntidade->setDado( "cod_entidade", $this->getCodigoEntidade() );
    $obTUsuarioEntidade->setDado( "exercicio",    $this->stExercicio          );

    return $obErro = $obTUsuarioEntidade->recuperaMembrosDisponiveis( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

/**
    * Lista membros selecionados
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarMembrosSelecionados(&$rsRecordSet, $stOrdem = "", $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoUsuarioEntidade.class.php"  );
    $obTUsuarioEntidade      = new TOrcamentoUsuarioEntidade;

    $obTUsuarioEntidade->setDado( "cod_entidade", $this->getCodigoEntidade() );
    $obTUsuarioEntidade->setDado( "exercicio",    $this->stExercicio          );

    return $obErro = $obTUsuarioEntidade->recuperaMembrosSelecionados( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

/**
    * Lista membros selecionados
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarUsuariosPermitidos(&$rsRecordSet, $stOrdem = "", $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoUsuarioEntidade.class.php"  );
    $obTUsuarioEntidade      = new TOrcamentoUsuarioEntidade;

    if($this->getCodigoEntidade())
        $stFiltro .= " AND OUE.cod_entidade = ".$this->getCodigoEntidade()." ";

    if($this->getExercicio())
        $stFiltro .= " AND OUE.exercicio = '".$this->getExercicio()."' ";

    $obErro = $obTUsuarioEntidade->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

    return $obErro;
}

/**
    * Lista usuários da entidade
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarUsuariosEntidade(&$rsRecordSet, $stOrdem = "", $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"         );
    $obTEntidade             = new TOrcamentoEntidade;

    $stFiltro = "";
    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= " ( E.cod_entidade || '-' || exercicio in (SELECT cod_entidade || '-' || exercicio ";
        $stFiltro .= " FROM orcamento.usuario_entidade WHERE numcgm = ".$this->obRCGM->getNumCGM()." AND exercicio = '".$this->stExercicio."') ";
        $stFiltro .= " OR E.exercicio < (SELECT substring(valor,7,4) from administracao.configuracao where parametro ='data_implantacao' and exercicio='".Sessao::getExercicio()."' and cod_modulo=9)) ";
        $stFiltro .= " AND E.exercicio = '".$this->stExercicio."' ";
    }
    if ($this->getVerificaConfiguracao()) {
        $stFiltro .= " AND NOT EXISTS ( SELECT 1                                            \n";
        $stFiltro .= "                    FROM administracao.configuracao ac                \n";
        $stFiltro .= "                   WHERE ac.valor::integer = E.cod_entidade           \n";
        $stFiltro .= "                     AND ac.exercicio      = E.exercicio              \n";
        $stFiltro .= "                     AND ac.parametro      = 'cod_entidade_camara')   \n";
    }
    $obErro = $obTEntidade->recuperaUsuariosEntidade( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
    
    return $obErro;
}

function listarEntidadeRestos(&$rsRecordSet, $stOrdem = "", $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
    $obTEntidade = new TOrcamentoEntidade;
    $obTEntidade->setDado('exercicio',  $this->stExercicio);
    $obTEntidade->setDado('valor', 't');
    $obErro = $obTEntidade->recuperaEntidadeRestos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
    return $obErro;
}

function verificaEntidadeRestos(&$rsRecordSet, $stOrdem = "", $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
    $obTEntidade = new TOrcamentoEntidade;
    $obTEntidade->setDado('exercicio',  $this->stExercicio);
    $obTEntidade->setDado('valor', 'f');
    $obErro = $obTEntidade->verificaEntidadeRestos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
    return $obErro;
}

function listarUsuariosEntidadeCnpj(&$rsRecordSet, $stOrdem = "", $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"         );
    $obTEntidade             = new TOrcamentoEntidade;

    $stFiltro = "";
    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= " E.cod_entidade in ( SELECT cod_entidade ";
        $stFiltro .= " FROM orcamento.usuario_entidade WHERE numcgm = ".$this->obRCGM->getNumCGM()." AND exercicio = '".$this->stExercicio."') ";
        $stFiltro .= " AND E.exercicio = '".$this->stExercicio."' ";
    }
    if ($this->inNumCGM) {
        if ($stFiltro != '') {
            $stFiltro .= " AND ";
        }

        $stFiltro .= " E.cod_entidade = ".$this->inNumCGM;
    }
    $obErro = $obTEntidade->recuperaUsuariosEntidadeCnpj( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

    return $obErro;
}

function listarEntidades(&$rsRecordSet, $stOrdem = "", $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"         );
    $obTEntidade             = new TOrcamentoEntidade;

    $stFiltro = "";
    $obTEntidade->setDado( "exercicio",    $this->stExercicio          );
    $obErro = $obTEntidade->recuperaEntidades( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

    return $obErro;
}

function verificaSituacaoEntidade(&$rsLista, $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"         );
    $obTEntidade             = new TOrcamentoEntidade;

    $obTEntidade->setDado( "cod_entidade", $this->getCodigoEntidade() );
    $obTEntidade->setDado( "exercicio",    $this->stExercicio          );
    $obErro = $obTEntidade->recuperaReceitaDespesaEntidade( $rsLista, $stFiltro, "", $boTransacao );

    return $obErro;
}

}
