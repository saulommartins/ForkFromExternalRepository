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
    * Classe de regra de negócio para Licença Diversa
    * Data de Criação: 01/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMLicencaDiversa.class.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.12
*/

/*
$Log$
Revision 1.8  2007/05/14 20:39:13  dibueno
Alterações para possibilitar a emissao do alvará diverso

Revision 1.7  2006/10/11 10:27:53  dibueno
*** empty log message ***

Revision 1.6  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaDiversa.class.php"                );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMElementoLicencaDiversa.class.php"        );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicenca.class.php"                            );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMTipoLicencaDiversa.class.php"                 );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php"                           );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoElemLicenDiversaValor.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoLicencaDiversaValor.class.php"   );
/**
* Classe de regra de negócio para Licença Diversa
* Data de Criação: 01/12/2004

* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Fábio Bertoldi Rodrigues

* @package URBEM
* @subpackage Regra
*/

class RCEMLicencaDiversa extends RCEMLicenca
{
/**
* Todos os metodos setters e getters sao herdados
* da classe mae RCEMLicenca.
*/

/**
* @access Private
*
var $obTCEMLicencaDiversa;
/**
* @access Private
* @var Array
*/
var $arElementos;

/**
* @access Private
* @var Integer
*/
var $inOcorrencia;
/**
* @access Private
* @var Integer
*/
var $stAcaoElemento;
/**
* @access Private
* @var Object
*/
var $obRCEMTipoLicencaDiversa;

//SETTERS
/**
* @access Public
* @param Integer $valor
*/
function setOcorrencia($valor) { $this->inOcorrencia = $valor; }
/**
* @access Public
* @param Integer $valor
*/
function setArrayElementos($valor) { $this->arElementos = $valor; }
//GETTERS
/**
* @access Public
* @return Integer
*/
function getArrayElementos() { return $this->arElementos; }
/**
* @access Public
* @return Integer
*/
function getOcorrencia() { return $this->inOcorrencia; }
//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCEMLicencaDiversa()
{
    parent::RCEMLicenca();
    $this->obTCEMLicencaDiversa         = new TCEMLicencaDiversa            ;
    $this->obTCEMElementoLicencaDiversa = new TCEMElementoLicencaDiversa    ;
    $this->obRCEMTipoLicencaDiversa     = new RCEMTipoLicencaDiversa        ;
    $this->obRCEMElemento               = new RCEMElemento( $obAtividade)   ;
    $this->obTransacao                  = new Transacao                     ;
    $this->obRCadastroDinamico          = new RCadastroDinamico             ;
    $this->obRCadastroDinamico->setPersistenteAtributos ( new TCEMAtributoTipoLicencaDiversa    );
    $this->obRCadastroDinamico->setPersistenteValores   ( new TCEMAtributoLicencaDiversaValor   );
    $this->obRCadastroDinamico->setCodCadastro( 4 );
    //$this->obRCadastroDinamico->obRModulo->setCodModulo( 14 );
    $this->obRCadastroDinamicoElemento = new RCadastroDinamico;
    $this->obRCadastroDinamicoElemento->setPersistenteAtributos ( new TCEMAtributoElemento              );
    $this->obRCadastroDinamicoElemento->setPersistenteValores   ( new TCEMAtributoElemLicenDiversaValor );
    $this->obRCadastroDinamicoElemento->setCodCadastro( 5 );
    //$this->obRCadastroDinamicoElemento->obRModulo->setCodModulo( 14 );
    $this->arElementos = array();
}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)

/**
* Inclui os dados setados na tabela de Licença
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function concederLicenca($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obErro = parent::concederLicenca( $boTransacao );

        if ( !$obErro->ocorreu() ) {

            $this->obTCEMLicencaDiversa->setDado( "cod_licenca" , $this->inCodigoLicenca                );
            $this->obTCEMLicencaDiversa->setDado( "exercicio"   , $this->stExercicio                    );
            $this->obTCEMLicencaDiversa->setDado( "cod_tipo"    , $this->obRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa()    );
            $this->obTCEMLicencaDiversa->setDado( "numcgm"      , $this->obRCGM->getNumCGM()            );
            $obErro = $this->obTCEMLicencaDiversa->inclusao( $boTransacao );
            #echo '<h2>LICENCA DIVERSA</h2>';
            //$this->obTCEMLicencaDiversa->debug();
            // atributos
            if (!$obErro->ocorreu() ) {
                //O Restante dos valores vem setado da página de processamento
                #echo '<h2>ATRIBUTO LICENCA DIVERSA</h2>';

                $arChaveAtributo =  array(
                            "cod_licenca"   => $this->getCodigoLicenca(),
                            "exercicio"     => $this->stExercicio       ,
                            "cod_tipo"      => $this->obRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa()
                        );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                $obErro =   $this->obRCadastroDinamico->salvarValores ( $boTransacao );

                if (!$obErro->ocorreu() ) {
                    $arElementos = $this->arElementos;

                    for ($inCount=0;$inCount < count($arElementos);$inCount++) {
                        $inCodElemento = $arElementos[$inCount]["inCodigoElemento"];
                        $this->obRCEMElemento->setCodigoElemento($inCodElemento);
                        $this->setOcorrencia( $arElementos[$inCount]["stOcorrencia"]);

                        foreach ($arElementos[$inCount]["elementos"][$inCodElemento] as $chave => $valor) {
                            //$inCodAtributo = substr($chave,strlen($chave)-5,3);
                            $this->obRCadastroDinamicoElemento->addAtributosDinamicos( $chave, $valor );
                        }
                        $obErro = $this->salvarElementoLicencaDiversa($boTransacao);
                        // limpar atributos do elemento
                        $this->obRCadastroDinamicoElemento->setAtributosDinamicos( array() );
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMLicencaDiversa );

    return $obErro;
}

#(cod_modulo,cod_cadastro,cod_atributo,cod_elemento)= (14,        5,              2,          4)

/**
* Altera os dados setados na tabela de Licença
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarLicenca($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMLicenca->setDado( "cod_licenca" , $this->inCodigoLicenca );
        $this->obTCEMLicenca->setDado( "exercicio" , $this->stExercicio );
        $this->obTCEMLicenca->setDado( "dt_inicio" , $this->dtDataInicio );
        $this->obTCEMLicenca->setDado( "dt_termino" , $this->dtDataTermino );
        $obErro = $this->obTCEMLicenca->alteracao( $boTransacao );
        if (!$obErro->ocorreu() ) {
          //O Restante dos valores vem setado da página de processamento
          $arChaveAtributo =  array(
                                 "cod_licenca"   => $this->getCodigoLicenca(),
                                 "exercicio"     => $this->stExercicio       ,
                                 "cod_tipo"      => $this->obRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa()
                                 );
          $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
          $obErro =   $this->obRCadastroDinamico->alterarValores ( $boTransacao );

        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMLicencaDiversa );

    return $obErro;
}

/**
* Baixa a Licença setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function baixarLicenca($boTransacao = "")
{
    $codigoTipoBaixa = "1";
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMBaixaLicenca->setDado( "cod_licenca" , $this->inCodigoLicenca );
        $this->obTCEMBaixaLicenca->setDado( "exercicio"   , $this->stExercicio     );
        $this->obTCEMBaixaLicenca->setDado( "dt_inicio"   , $this->dtDataInicio    );
        $this->obTCEMBaixaLicenca->setDado( "cod_tipo"    , $codigoTipoBaixa       );
        $this->obTCEMBaixaLicenca->setDado( "motivo"      , $this->stMotivo        );
        $obErro = $this->obTCEMBaixaLicenca->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRProcesso->getCodigoProcesso() ) {
                $this->obTCEMProcessoBaixaLicenca->setDado( "cod_licenca"  , $this->inCodigoLicenca                  );
                $this->obTCEMProcessoBaixaLicenca->setDado( "exercicio"    , $this->stExercicio                      );
                $this->obTCEMProcessoBaixaLicenca->setDado( "cod_processo" , $this->obRProcesso->getCodigoProcesso() );
                $this->obTCEMProcessoBaixaLicenca->setDado( "dt_inicio"    , $this->dtDataInicio                     );
                $obErro = $this->obTCEMProcessoBaixaLicenca->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMLicencaDiversa );

    return $obErro;
}

/**
* Suspende a Licença setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function suspenderLicenca($boTransacao = "")
{
    $codigoTipoBaixa = "2";
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMBaixaLicenca->setDado( "cod_licenca" , $this->inCodigoLicenca );
        $this->obTCEMBaixaLicenca->setDado( "exercicio"   , $this->stExercicio     );
        $this->obTCEMBaixaLicenca->setDado( "dt_inicio"   , $this->dtDataInicio    );
        $this->obTCEMBaixaLicenca->setDado( "dt_termino"  , $this->dtDataTermino   );
        $this->obTCEMBaixaLicenca->setDado( "cod_tipo"    , $codigoTipoBaixa       );
        $this->obTCEMBaixaLicenca->setDado( "motivo"      , $this->stMotivo        );
        $obErro = $this->obTCEMBaixaLicenca->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRProcesso->getCodigoProcesso() ) {
                $this->obTCEMProcessoBaixaLicenca->setDado( "cod_licenca"  , $this->inCodigoLicenca         );
                $this->obTCEMProcessoBaixaLicenca->setDado( "exercicio"    , $this->stExercicio             );
                $this->obTCEMProcessoBaixaLicenca->setDado( "cod_processo" , $this->obRProcesso->getCodigoProcesso() );
                $this->obTCEMProcessoBaixaLicenca->setDado( "dt_inicio"    , $this->dtDataInicio            );
                $obErro = $this->obTCEMProcessoBaixaLicenca->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMLicencaDiversa );

    return $obErro;
}

/**
* Cancela a suspensão da Licença setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function cancelarSuspensao($boTransacao = "")
{
    $codigoTipoBaixa = "2";
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMBaixaLicenca->setDado( "cod_licenca" , $this->inCodigoLicenca );
        $this->obTCEMBaixaLicenca->setDado( "exercicio"   , $this->stExercicio     );
        $this->obTCEMBaixaLicenca->setDado( "dt_inicio"   , $this->dtDataInicio    );
        $this->obTCEMBaixaLicenca->setDado( "dt_termino"  , $this->dtDataTermino   );
        $this->obTCEMBaixaLicenca->setDado( "cod_tipo"    , $codigoTipoBaixa       );
        $obErro = $this->obTCEMBaixaLicenca->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMLicencaDiversa );

    return $obErro;
}

/**
* Cassa a Licença setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function cassarLicenca($boTransacao = "")
{
    $codigoTipoBaixa = "3";
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMBaixaLicenca->setDado( "cod_licenca" , $this->inCodigoLicenca );
        $this->obTCEMBaixaLicenca->setDado( "exercicio"   , $this->stExercicio     );
        $this->obTCEMBaixaLicenca->setDado( "dt_inicio"   , $this->dtDataInicio    );
        $this->obTCEMBaixaLicenca->setDado( "cod_tipo"    , $codigoTipoBaixa       );
        $this->obTCEMBaixaLicenca->setDado( "motivo"      , $this->stMotivo        );
        $obErro = $this->obTCEMBaixaLicenca->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRProcesso->getCodigoProcesso() ) {
                $this->obTCEMProcessoBaixaLicenca->setDado( "cod_licenca"  , $this->inCodigoLicenca         );
                $this->obTCEMProcessoBaixaLicenca->setDado( "exercicio"    , $this->stExercicio             );
                $this->obTCEMProcessoBaixaLicenca->setDado( "cod_processo",$this->obRProcesso->getCodigoProcesso());
                $this->obTCEMProcessoBaixaLicenca->setDado( "dt_inicio"    , $this->dtDataInicio            );
                $obErro = $this->obTCEMProcessoBaixaLicenca->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMLicencaDiversa );

    return $obErro;
}

/**
* Lista as Licenças Ativas conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarLicencas(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
//    $stFiltro = "";
    if ($this->inCodigoLicenca) {
        $stFiltro .= "\n cod_licenca = ".$this->inCodigoLicenca." AND ";
    }
    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= "\n numcgm = ".$this->obRCGM->getNumCGM()." AND ";
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= "\n exercicio = ".$this->getExercicio()." AND ";
    }
    $stFiltro .= "\n especie_licenca= 'Diversa'  AND ";

    if ($stFiltro) {
        $stFiltro = "\r\n WHERE \r\n\t ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrder = "\r\n ORDER BY cod_licenca ";
    $obErro = $this->obVCEMLicencaAtiva->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
//	$this->obVCEMLicencaAtiva->debug();
    return $obErro;
}

/**
* Lista as Licenças Suspensas Ativas conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarLicencasSuspensasAtivas(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoLicenca) {
        $stFiltro .= " cod_licenca = ".$this->inCodigoLicenca." AND ";
    }
    if ( $this->obRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " inscricao_economica = ".$this->obRCEMInscricaoEconomica->getInscricaoEconomica()." AND ";
    }
    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= " numcgm = ".$this->obRCGM->getNumCGM()." AND ";
    }
    $stOrder = " ORDER BY cod_licenca ";
    $obErro = $this->obVCEMLicencaSuspensaAtiva->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Recupera do banco de dados os dados da Licença selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarLicenca($boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoLicenca) {
        $stFiltro .= " cod_licenca = ".$this->inCodigoLicenca." AND ";
    }
    $stOrder = " ORDER BY cod_licenca ";
    $obErro = $this->obVCEMLicencaAtiva->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->inCodigoLicenca = $rsRecordSet->getCampo( "cod_licenca" );
        $this->stExercicio     = $rsRecordSet->getCampo( "exercicio"   );
        $this->dtDataInicio    = $rsRecordSet->getCampo( "dt_inicio"   );
        $this->dtDataTermino   = $rsRecordSet->getCampo( "dt_termino"  );
    }

    return $obErro;

}

/**
* Inclui os dados na tabela elemento licenca diversa e com seus respectivos atributos
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function salvarElementoLicencaDiversa($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        /* Antes de Incluir, limpa atributos */
 //       $this->excluirAtributosElementoLicencaDiversa($boTransacao);
        #echo '<h2>ELEMENTO LICENCA DIVERSA</h2>';
        $this->obTCEMElementoLicencaDiversa->setDado( "cod_elemento", $this->obRCEMElemento->getCodigoElemento() );
        $this->obTCEMElementoLicencaDiversa->setDado( "cod_licenca" , $this->getCodigoLicenca                 () );
        $this->obTCEMElementoLicencaDiversa->setDado( "exercicio"   , $this->getExercicio                     () );
        $this->obTCEMElementoLicencaDiversa->setDado( "cod_tipo"    , $this->obRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa() );
        $this->obTCEMElementoLicencaDiversa->setDado( "ocorrencia"  , $this->getOcorrencia                    () );
        $obErro = $this->obTCEMElementoLicencaDiversa->inclusao( $boTransacao );
        #$this->obTCEMElementoLicencaDiversa->debug(); #exit;

        if ( !$obErro->ocorreu() ) {
            //O Restante dos valores vem setado da página de processamento
            $arChaveAtributo =  array(
                        "cod_elemento"  => $this->obRCEMElemento->getCodigoElemento         () ,
                        "cod_tipo"      => $this->obRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa   () ,
                        "cod_licenca"   => $this->getCodigoLicenca                          () ,
                        "exercicio"     => $this->getExercicio                              () ,
                        "ocorrencia"    => $this->getOcorrencia                             ()
                    );
            $this->obRCadastroDinamicoElemento->setChavePersistenteValores( $arChaveAtributo );
            $obErro = $this->obRCadastroDinamicoElemento->salvarValores ( $boTransacao );
        }

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMElementoLicencaDiversa );

    return $obErro;
}

/**
* Inclui os dados na tabela elemento licenca diversa e com seus respectivos atributos
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function salvarElementoTipoLicencaDiversa($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        /* Antes de Incluir, limpa atributos */
        // $this->excluirAtributosElementoLicencaDiversa($boTransacao);
        #echo '<h2>ELEMENTO TIPO LICENCA DIVERSA</h2>';
        $this->obTCEMElementoLicencaDiversa->setDado( "cod_elemento", $this->obRCEMElemento->getCodigoElemento() );
        $this->obTCEMElementoLicencaDiversa->setDado( "cod_licenca" , $this->getCodigoLicenca                 () );
        $this->obTCEMElementoLicencaDiversa->setDado( "exercicio"   , $this->getExercicio                     () );
        $this->obTCEMElementoLicencaDiversa->setDado( "cod_tipo"    , $this->obRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa() );
        $this->obTCEMElementoLicencaDiversa->setDado( "ocorrencia"  , $this->getOcorrencia                    () );
        $obErro = $this->obTCEMElementoLicencaDiversa->inclusao( $boTransacao );
        #$this->obTCEMElementoLicencaDiversa->debug(); #exit;

        if ( !$obErro->ocorreu() ) {
            //O Restante dos valores vem setado da página de processamento
            $arChaveAtributo =  array(
                        "cod_elemento"  => $this->obRCEMElemento->getCodigoElemento         () ,
                        "cod_tipo"      => $this->obRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa   () ,
                        "cod_licenca"   => $this->getCodigoLicenca                          () ,
                        "exercicio"     => $this->getExercicio                              () ,
                        "ocorrencia"    => $this->getOcorrencia                             ()
                    );
            $this->obRCadastroDinamicoElemento->setChavePersistenteValores( $arChaveAtributo );
            $obErro = $this->obRCadastroDinamicoElemento->salvarValores ( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMElementoLicencaDiversa );

    return $obErro;
}

/**
    * Lista os Elementos da Licenca
    * @access Public
    * @param  Object $rsRecordSet
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarElementoLicencaDiversa(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoElemento) {
        $stFiltro .= " AND e.cod_elemento = ".$this->inCodigoElemento;
    }
    if ($this->inCodigoLicenca) {
        $stFiltro .= " AND l.cod_licenca = ".$this->inCodigoLicenca;
    }
    $stOrdem = " ORDER BY eL.ocorrencia ";
    $obErro = $this->obTCEMElementoLicencaDiversa->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/* Limpa Atributos anteriores do elemento a ser salvo */
function excluirAtributosElementoLicencaDiversa($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //O Restante dos valores vem setado da página de processamento
        $arChaveAtributo =  array(
//                            "cod_elemento"  => $this->obRCEMElemento->getCodigoElemento         () ,
                            "cod_tipo"      => $this->obRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa   () ,
                            "cod_licenca"   => $this->getCodigoLicenca                          () ,
                            "exercicio"     => $this->getExercicio                              () ,
                            "ocorrencia"    => $this->getOcorrencia                             ()
                            );
        $this->obRCadastroDinamicoElemento->setChavePersistenteValores( $arChaveAtributo );
        $obErro =   $this->obRCadastroDinamicoElemento->excluirValores ( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMElementoLicencaDiversa->setDado("cod_elemento",$this->obRCEMElemento->getCodigoElemento());
            $this->obTCEMElementoLicencaDiversa->setDado( "cod_licenca" , $this->getCodigoLicenca               ());
            $this->obTCEMElementoLicencaDiversa->setDado( "exercicio"   , $this->getExercicio                   ());
            $this->obTCEMElementoLicencaDiversa->setDado( "cod_tipo"    , $this->obRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa() );
            $this->obTCEMElementoLicencaDiversa->setDado( "ocorrencia"  , $this->getOcorrencia                  ());
            $obErro = $this->obTCEMElementoLicencaDiversa->exclusao( $boTransacao );
        }
    }
}
}
