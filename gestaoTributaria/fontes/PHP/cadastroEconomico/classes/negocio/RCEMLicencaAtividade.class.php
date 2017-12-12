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
    * Classe de regra de negócio para Licença Atividade
    * Data de Criação: 01/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMLicencaAtividade.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.12
*/

/*
$Log$
Revision 1.11  2007/03/14 20:03:50  dibueno
Alterações para busca de dados da licenca com exercicio '0000'

Revision 1.10  2006/11/17 16:36:51  dibueno
Bug #7093#

Revision 1.9  2006/10/23 16:19:27  dibueno
Alterações para emissão de alvará e inclusão de observação

Revision 1.8  2006/10/10 15:13:33  dibueno
Adição do ""

Revision 1.7  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaAtividade.class.php"   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicenca.class.php"               );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoAtividade.class.php"    );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php"    );

/**
* Classe de regra de negócio para Licença Atividade
* Data de Criação: 01/12/2004

* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Fábio Bertoldi Rodrigues

* @package URBEM
* @subpackage Regra
*/

class RCEMLicencaAtividade extends RCEMLicenca
{
/**
* @access Private
* @var Object
*/
var $obTCEMLicencaAtividade;
/**
* @access Private
* @var Object
*/
var $obRCEMInscricaoAtividade;
/**
* @access Private
* @var Array
*/
var $arAtividades;
var $inOcorrenciaLicenca;

//SETTERS
/**
* @access Public
* @param Array $valor
*/
function setAtividades($valor) { $this->arAtividades = $valor; }
function setOcorrenciaLicenca($valor) { $this->inOcorrenciaLicenca = $valor; }

//GETTERS
/**
* @access Public
* @return Array
*/
function getAtividades() { return $this->arAtividades; }
function getOcorrenciaLicenca() { return $this->inOcorrenciaLicenca; }

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCEMLicencaAtividade()
{
    parent::RCEMLicenca();
    $this->obTCEMLicencaAtividade   = new TCEMLicencaAtividade;
    $this->obTransacao              = new Transacao;
    $this->obRCEMInscricaoAtividade = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );
    $this->arAtividades             = array();
}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)

/**
* Inclui os dados setados na tabela de Licença Atividade
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function concederLicenca($boTransacao = "")
{
    ;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( count( $this->arAtividades ) < 1  ) {
            $obErro->setDescricao( "Deve ser informado ao menos uma Atividade!" );
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = parent::concederLicenca( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $IE = $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica();
                $stFiltro = " WHERE inscricao_economica = ". $IE;
                $stOrdem  = " ORDER BY ocorrencia_licenca DESC limit 1 ";
                $this->obTCEMLicencaAtividade->recuperaTodos ( $rsLicenca, $stFiltro, $stOrdem, $boTransacao );
//                $this->obTCEMLicencaAtividade->debug();exit;
                if ( $rsLicenca->getNumLinhas() < 1 ) {
                    $this->inOcorrenciaLicenca = 1;
                } else {
                    $this->inOcorrenciaLicenca = $rsLicenca->getCampo('ocorrencia_licenca') + 1;
                }

                foreach ($this->arAtividades as $novaAtividade => $arAtividades) {

                    if (!$this->stExercicio) {
                        $this->stExercicio = '0000';
                    }

                    $this->obTCEMLicencaAtividade->setDado( "cod_licenca"          , $this->inCodigoLicenca    );
                    $this->obTCEMLicencaAtividade->setDado( "exercicio"            , $this->stExercicio        );
                    $this->obTCEMLicencaAtividade->setDado( "cod_atividade"        , $arAtividades["cod_atividade"]        );
                    $this->obTCEMLicencaAtividade->setDado( "inscricao_economica"  , $IE );
                    $this->obTCEMLicencaAtividade->setDado( "ocorrencia_atividade" , $arAtividades["ocorrencia_atividade"] );
                    $this->obTCEMLicencaAtividade->setDado( "ocorrencia_licenca"   , $this->inOcorrenciaLicenca);
                    $this->obTCEMLicencaAtividade->setDado( "dt_inicio"            , $this->dtDataInicio       );
                    if ($this->dtDataTermino) {
                        $this->obTCEMLicencaAtividade->setDado( "dt_termino"       , $this->dtDataTermino      );
                    }
                    $obErro = $this->obTCEMLicencaAtividade->inclusao ( $boTransacao );
                    #$this->obTCEMLicencaAtividade->debug();

                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMLicencaAtividade );

    return $obErro;
}

/**
* Altera os dados setados na tabela de Licença Atividade
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarLicenca($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( count( $this->arAtividades ) < 1  ) {
            $obErro->setDescricao( "Deve ser informado ao menos uma Atividade!" );
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = parent::alterarLicenca( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $IE = $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica();
                $stFiltro = " WHERE inscricao_economica = ". $IE;
                $stOrdem  = " ORDER BY ocorrencia_licenca DESC limit 1 ";
                $this->obTCEMLicencaAtividade->recuperaTodos ($rsLicenca,$stFiltro,$stOrdem,$boTransacao);
                if ( $rsLicenca->getNumLinhas() < 1 ) {
                    $this->setOcorrenciaLicenca ( 1 );
                } else {
                    $this->setOcorrenciaLicenca ( $rsLicenca->getCampo('ocorrencia_licenca') );
                }

                $obTCEMLicenca   = new RCEMLicenca;
                $stCondicao = " WHERE cod_licenca = ".$this->inCodigoLicenca;
                $obTCEMLicenca->obTCEMLicencaDocumento->listarLicencas($rsCodLicenca, $stCondicao, $boTransacao  );

                if ( $rsCodLicenca->getNumLinhas() < 1 ) {
                     $obTCEMLicenca->obTCEMLicencaDocumento->BuscaUltimoNumeroAlvara( $rsAlvara , $boTransacao);
                     $inNumAlvara = $rsAlvara->getCampo('valor') + 1;

                     $obTCEMLicenca->obTCEMLicencaDocumento->setDado('exercicio', $this->getExercicio() );
                     $obTCEMLicenca->obTCEMLicencaDocumento->setDado('cod_licenca', $this->inCodigoLicenca );
                     $obTCEMLicenca->obTCEMLicencaDocumento->setDado('cod_tipo_documento', $_REQUEST['inCodTipoDocumento'] );
                     $obTCEMLicenca->obTCEMLicencaDocumento->setDado('cod_documento', $_REQUEST['stCodDocumento'] );
                     $obTCEMLicenca->obTCEMLicencaDocumento->setDado('num_alvara', $inNumAlvara);
                     $obErro = $obTCEMLicenca->obTCEMLicencaDocumento->inclusao ( $boTransacao );

                     if ( $obErro->ocorreu() ) {
                        break;
                     }
                }

                if ( !$obErro->ocorreu() ) {
                    $this->setOcorrenciaLicenca ( $this->getOcorrenciaLicenca () +1);
                    foreach ($this->arAtividades as $atividades => $arAtividades) {
                        $this->obTCEMLicencaAtividade->setDado( "cod_licenca"          , $this->inCodigoLicenca                );
                        $this->obTCEMLicencaAtividade->setDado( "exercicio"            , $this->stExercicio                    );
                        $this->obTCEMLicencaAtividade->setDado( "cod_atividade"        , $arAtividades["cod_atividade"]        );
                        $this->obTCEMLicencaAtividade->setDado( "inscricao_economica"  , $IE );
                        $this->obTCEMLicencaAtividade->setDado( "ocorrencia_atividade" , $arAtividades["ocorrencia_atividade"] );
                        $this->obTCEMLicencaAtividade->setDado( "ocorrencia_licenca"   , $this->getOcorrenciaLicenca()         );
                        $this->obTCEMLicencaAtividade->setDado( "dt_inicio"            , $this->dtDataInicio                   );
                        if ($this->dtDataTermino) {
                            $this->obTCEMLicencaAtividade->setDado( "dt_termino"       , $this->dtDataTermino                  );
                        }
                        $obErro = $this->obTCEMLicencaAtividade->inclusao( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
            }
        }
    }

    $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTCEMLicencaAtividade);

    return $obErro;
}

/**
* Recupera do banco de dados as atividades da Licença selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarAtividades(&$rsAtividades, $boTransacao = "")
{
    $stFiltro = "";
//    $stFiltro .= " lca.dt_termino is null AND ";
    if ($this->inCodigoLicenca) {
        $stFiltro .= " lca.cod_licenca = ".$this->inCodigoLicenca." AND ";
    }
    if ($this->stExercicio) {
        $stFiltro .= " lca.exercicio = '".$this->stExercicio."' AND ";
    }
    if ($this->inOcorrenciaLicenca) {
        $stFiltro .= " lca.ocorrencia_licenca = ". $this->inOcorrenciaLicenca . " AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cod_licenca ";
    $obErro = $this->obTCEMLicencaAtividade->recuperaRelacionamento( $rsAtividades, $stFiltro, $stOrder, $boTransacao );
    //$this->obTCEMLicencaAtividade->debug();
    return $obErro;
}

}
