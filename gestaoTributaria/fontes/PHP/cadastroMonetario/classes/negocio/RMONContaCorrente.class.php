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
    * Classe de regra de negócio para MONETARIO.CONTA_CORRENTE
    * Data de Criação: 31/10/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva

    * @package URBEM
    * @subpackage Regra

    * $Id: RMONContaCorrente.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.03
*/

/*
$Log$
Revision 1.9  2007/07/26 15:45:47  tonismar
Resolvidos problemas no método consultar

Revision 1.8  2006/09/15 14:46:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php"          );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONContaCorrente.class.php"   );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONTipoConta.class.php"   );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php"   );

class RMONContaCorrente
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoConta;
/**
    * @access Private
    * @var String
*/
var $stNumeroConta;
/**
    * @access Private
    * @var Date
*/
var $dtDataCriacao;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoTipoConta;
/**
    * @access Private
    * @var Object
*/
var $obTMONContaCorrente;
/**
    * @access Private
    * @var Object
*/
var $obTMONTipoConta;

var $boVinculoPlanoBanco;

var $inCodEntidadeVinculo;

//SETTERS
function setCodigoConta($valor) { $this->inCodigoConta     = $valor; }
function setNumeroConta($valor) { $this->stNumeroConta     = $valor; }
function setNumeroBanco($valor) { $this->inNumBanco        = $valor; }
function setNumeroAgencia($valor) { $this->inNumAgencia    = $valor; }
function setDataCriacao($valor) { $this->dtDataCriacao     = $valor; }
function setCodigoTipoConta($valor) { $this->inCodigoTipoConta = $valor; }

//GETTERS
function getCodigoConta() { return $this->inCodigoConta;     }
function getNumeroConta() { return $this->stNumeroConta;     }
function getNumeroBanco() { return $this->inNumBanco;        }
function getNumeroAgencia() { return $this->inNumAgencia;    }
function getDataCriacao() { return $this->dtDataCriacao;     }
function getCodigoTipoConta() { return $this->inCodigoTipoConta; }

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Private
*/
function RMONContaCorrente()
{
    $this->obTMONContaCorrente = new TMONContaCorrente;
    $this->obRMONAgencia       = new RMONAgencia;
    $this->obTMONTipoConta     = new TMONTipoConta();
}
/**
* Inclui os dados setados na tabela Monetaria ContaCorrente
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluirContaCorrente($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obRMONAgencia->consultarAgencia($rsAgencia, $boTransacao);
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->verificaContaCorrente($boTransacao);
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->obTMONContaCorrente->proximoCod( $this->inCodigoConta , $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTMONContaCorrente->setDado( "cod_conta_corrente",  $this->getCodigoConta()                          );
                    $this->obTMONContaCorrente->setDado( "num_conta_corrente",  $this->getNumeroConta()                          );
                    $this->obTMONContaCorrente->setDado( "data_criacao",        $this->getDataCriacao()                          );
                    $this->obTMONContaCorrente->setDado( "cod_banco",           $this->obRMONAgencia->obRMONBanco->getCodBanco() );
                    $this->obTMONContaCorrente->setDado( "cod_agencia",         $this->obRMONAgencia->getCodAgencia()            );
                    $this->obTMONContaCorrente->setDado( "cod_tipo",            $this->getCodigoTipoConta()                      );
                    $obErro = $this->obTMONContaCorrente->inclusao( $boTransacao );
                }
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONContaCorrente );

    return $obErro;
}

/**
* Altera os dados setados na tabela Monetaria ContaCorrente
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarContaCorrente($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTMONContaCorrente->setDado( "cod_banco",          $this->obRMONAgencia->obRMONBanco->getCodBanco() );
        $this->obTMONContaCorrente->setDado( "cod_agencia",        $this->obRMONAgencia->getCodAgencia() );
        $this->obTMONContaCorrente->setDado( "cod_conta_corrente", $this->getCodigoConta() );
        $this->obTMONContaCorrente->setDado( "num_conta_corrente", $this->getNumeroConta() );
        $this->obTMONContaCorrente->setDado( "data_criacao",       $this->getDataCriacao() );
        $this->obTMONContaCorrente->setDado( "cod_tipo",            $this->getCodigoTipoConta()                     );
        $obErro = $this->obTMONContaCorrente->alteracao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONContaCorrente );

    return $obErro;
}

/**
* Exclui os dados setados na tabela Monetaria ContaCorrente
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function excluirContaCorrente($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php" );
        $obRMONConvenio = new RMONConvenio;
        $obRMONConvenio->setCodigoConta ( $this->getCodigoConta() );
        $obRMONConvenio->ListarConvenio( $rsLista );

        if ( $rsLista->getNumLinhas() < 1 ) {

            $this->obTMONContaCorrente->setDado( "cod_conta_corrente", $this->getCodigoConta());
            $this->obTMONContaCorrente->setDado( "cod_banco"         , $this->obRMONAgencia->obRMONBanco->getCodBanco() );
            $this->obTMONContaCorrente->setDado( "cod_agencia"       , $this->obRMONAgencia->getCodAgencia() );
            $obErro = $this->obTMONContaCorrente->exclusao( $boTransacao );

        } else {

            $obErro->setDescricao ( "[A Conta Corrente possui um ou mais convênios vinculados]");
        }

    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONContaCorrente );

    return $obErro;
}

/**
* Lista as Agencias conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarContaCorrente(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->obRMONAgencia->obRMONBanco->getNumBanco() ) {
        $stFiltro .= " num_banco = '".$this->obRMONAgencia->obRMONBanco->getNumBanco()."' AND ";
    }
    if ( $this->obRMONAgencia->obRMONBanco->getCodBanco() ) {
        $stFiltro .= " ban.cod_banco = ".$this->obRMONAgencia->obRMONBanco->getCodBanco()." AND ";
    }
    if ( $this->obRMONAgencia->getCodAgencia() ) {
        $stFiltro .= " Ag.cod_agencia = ".$this->obRMONAgencia->getCodAgencia()." AND ";
    }
    if ( $this->obRMONAgencia->getNumAgencia() ) {
        $stFiltro .= " num_agencia = '".$this->obRMONAgencia->getNumAgencia()."' AND ";
    }
    if ( $this->getCodigoConta() ) {
        $stFiltro .= " cod_conta_corrente = ".$this->getCodigoConta()." AND ";
    }
    if ( $this->getNumeroConta() ) {
        $stFiltro .= " num_conta_corrente = '".$this->getNumeroConta()."' AND ";
    }
    if ( $this->getDataCriacao() ) {
        $stFiltro .= " data_criacao = '".$this->getDataCriacao()."' AND ";
    }
    if ($this->boVinculoPlanoBanco) {
        $stFiltro .= " EXISTS ( SELECT 1
                                  FROM contabilidade.plano_banco
                                  JOIN contabilidade.plano_analitica
                                    ON plano_analitica.cod_plano = plano_banco.cod_plano
                                   AND plano_analitica.exercicio = plano_banco.exercicio
                                  JOIN contabilidade.plano_conta
                                    ON plano_conta.cod_conta = plano_analitica.cod_conta
                                   AND plano_conta.exercicio = plano_analitica.exercicio
                                 WHERE plano_banco.cod_banco = CCor.cod_banco
                                   AND plano_banco.cod_agencia = CCor.cod_agencia
                                   AND plano_banco.cod_conta_corrente = CCor.cod_conta_corrente
                                   AND plano_conta.cod_estrutural NOT LIKE '1.1.1.1.3%'
                                   AND plano_banco.exercicio = '".Sessao::getExercicio()."' ";
        if ($this->inCodEntidadeVinculo != '') {
            $stFiltro .= 'AND plano_banco.cod_entidade IN ('.$this->inCodEntidadeVinculo.') ';
        }

        $stFiltro .= ") AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrder = " ORDER BY ban.cod_banco, Ag.cod_agencia, cod_conta_corrente ";
    $obErro = $this->obTMONContaCorrente->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
* Recupera do BD os dados da Conta Corrente selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarContaCorrente(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodigoConta() ) {
        $stFiltro .= " cod_conta_corrente = ".$this->getCodigoConta()." AND ";
    }
    if ( $this->getNumeroConta() ) {
        $stFiltro .= " num_conta_corrente = '".$this->getNumeroConta()."' AND ";
    }
    if ( $this->getDataCriacao() ) {
        $stFiltro .= " data_criacao = '".$this->getDataCriacao()."' AND ";
    }
    if ( $this->obRMONAgencia->getNumAgencia() ) {
        $stFiltro .= " num_agencia = '".$this->obRMONAgencia->getNumAgencia()."' AND ";
    }
    if ( $this->obRMONAgencia->obRMONBanco->getCodBanco() ) {
        $stFiltro .= " Ban.cod_banco = '".$this->obRMONAgencia->obRMONBanco->getCodBanco()."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY num_conta_corrente ";

    $obErro = $this->obTMONContaCorrente->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {

        $this->obRMONAgencia->consultarAgencia($boTransacao);

        $this->obRMONAgencia->setCodAgencia( $rsRecordSet->getCampo("cod_agencia") );
        $this->obRMONAgencia->setNumAgencia( $rsRecordSet->getCampo("num_agencia") );
        $this->obRMONAgencia->setNomAgencia( $rsRecordSet->getCampo("nom_agencia") );
        $this->obRMONAgencia->obRMONBanco->setCodBanco( $rsRecordSet->getCampo("cod_banco") );
        $this->obRMONAgencia->obRMONBanco->setNumBanco( $rsRecordSet->getCampo("num_banco") );
        $this->obRMONAgencia->obRMONBanco->setNomBanco( $rsRecordSet->getCampo("nom_banco") );
        $this->setCodigoConta( $rsRecordSet->getCampo("cod_conta_corrente") );
        $this->setNumeroConta( $rsRecordSet->getCampo("num_conta_corrente") );
        $this->setDataCriacao( $rsRecordSet->getCampo("nom_agencia")        );
        $this->setCodigoTipoConta( $rsRecordSet->getCampo("cod_tipo") );
    }

    return $obErro;

}

/**
    * Verifica se a ContaCorrente a ser incluida, já não existe
    * @access Public
    * @param  Object RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaContaCorrente($boTransacao = "")
{
    $stFiltro  = " ccor.num_conta_corrente = '".$this->getNumeroConta()."' AND ";
    $stFiltro .= " ban.num_banco          = '".$this->obRMONAgencia->obRMONBanco->getNumBanco()."' AND ";
    $stFiltro .= " ag.cod_agencia        = ".$this->obRMONAgencia->getCodAgencia()." AND";
    $stFiltro  = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    $obErro = $this->obTMONContaCorrente->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    if ( $rsRecordSet->getNumLinhas() > 0 && $rsRecordSet->getCampo("num_conta_corrente") == $this->stNumeroConta ) {
        $obErro->setDescricao("Conta Corrente já cadastrada na agência informada! ($this->stNumeroConta)");
    }

    return $obErro;
}

/**
    * Verifica se a ContaCorrente a ser incluida, já não existe
    * @access Public
    * @param  Object RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function existeContaCorrente(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro  = " WHERE num_conta_corrente = '". trim ($this->getNumeroConta())."'";

    $obErro = $this->obTMONContaCorrente->recuperaExisteContaCorrente( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Retorna os tipos de conta ordenadas pela descrição
    * @access Public
    * @param  Object RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function listarTipoConta(&$rsRecordSet,$boTrasacao="")
{
    $obErro=$this->obTMONTipoConta->recuperaTodos($rsRecordSet,"","descricao",$boTransacao);

    return $obErro;
}

/**
	* Lista as Contas Correntes conforme o filtro setado
	* @access Public
	* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
	* @param  Object $obTransacao Parâmetro Transação
	* @return Object Objeto Erro
*/
function listarContaCorrenteConciliacao(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ($this->boVinculoPlanoBanco) {
        $this->obTMONContaCorrente->setDado('exercicio', Sessao::getExercicio());
        if ($this->inCodEntidadeVinculo != '') {
            $this->obTMONContaCorrente->setDado('entidades', $this->inCodEntidadeVinculo);
        }
    }
    
    if ( $this->getNumeroConta() ) {
        $this->obTMONContaCorrente->setDado('num_conta_corrente', $this->getNumeroConta());
    }
    
    if ( $this->getNumeroAgencia() ) {
        $this->obTMONContaCorrente->setDado('inNumAgencia', $this->getNumeroAgencia());
    }
    
    if ( $this->getNumeroBanco() ) {
        $this->obTMONContaCorrente->setDado('inNumBanco', $this->getNumeroBanco());
    }

    $stOrder = " ORDER BY plano_banco.cod_entidade, ban.cod_banco, Ag.cod_agencia, cod_conta_corrente ";
    $obErro = $this->obTMONContaCorrente->recuperaContaCorrenteConciliacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
	* Lista as Contas Correntes e Contas Banco conforme o filtro setado
	* @access Public
	* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
	* @param  Object $obTransacao Parâmetro Transação
	* @return Object Objeto Erro
*/
function listarContaCorrentePlanoBanco(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ($this->boVinculoPlanoBanco) {
        $this->obTMONContaCorrente->setDado('exercicio', Sessao::getExercicio());
        if ($this->inCodEntidadeVinculo != '') {
            $this->obTMONContaCorrente->setDado('entidades', $this->inCodEntidadeVinculo);
        }
    }
    
    if ( $this->getNumeroConta() ) {
        $this->obTMONContaCorrente->setDado('num_conta_corrente', $this->getNumeroConta());
    }

    $stOrder = " ORDER BY plano_banco.cod_entidade, ban.cod_banco, Ag.cod_agencia, cod_conta_corrente ";
    $obErro = $this->obTMONContaCorrente->recuperaContaCorrentePlanoBanco( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
