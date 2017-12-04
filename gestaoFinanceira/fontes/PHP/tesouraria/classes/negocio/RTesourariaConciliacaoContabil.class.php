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
    * Classe de Regra do Relatório de Situação de Empenho
    * Data de Criação   : 06/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.19
*/

/*
$Log$
Revision 1.3  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php"                         );

/**
    * Classe de Regra de Negócios Conciliação Contabil
    * @author Desenvolvedor: Cleisson Barboza
*/
class RTesourariaConciliacaoContabil
{
/*
    * @var Object
    * @access Private
*/
var $roRTesourariaConciliacao;

/*
    * @var Object
    * @access Private
*/
var $obRContabilidadeLancamentoValor;

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaConciliacaoContabil($roRTesourariaConciliacao)
{
    $this->obRContabilidadeLancamentoValor   = new RContabilidadeLancamentoValor;
    $this->roRTesourariaConciliacao    = &$roRTesourariaConciliacao;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"                      );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaConciliacaoLancamentoContabil.class.php"   );
    $obTransacao      =  new Transacao;
    $obTTesourariaConciliacaoContabil =  new TTesourariaConciliacaoLancamentoContabil;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( !$obErro->ocorreu() ) {
            $obTTesourariaConciliacaoContabil->setDado( "cod_plano" ,   $this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getCodPlano());
            $obTTesourariaConciliacaoContabil->setDado( "exercicio" ,   $this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getExercicio());
            $obTTesourariaConciliacaoContabil->setDado( "cod_lote",     $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote());
            $obTTesourariaConciliacaoContabil->setDado( "tipo",         $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getTipo());
            $obTTesourariaConciliacaoContabil->setDado( "sequencia",    $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->getSequencia());
            $obTTesourariaConciliacaoContabil->setDado( "cod_entidade", $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade());
            $obTTesourariaConciliacaoContabil->setDado( "tipo_valor",   $this->obRContabilidadeLancamentoValor->getTipoValor());
            $obTTesourariaConciliacaoContabil->setDado( "mes",          $this->roRTesourariaConciliacao->getMes());
            $obErro = $obTTesourariaConciliacaoContabil->inclusao( $boTransacao );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaConciliacaoContabil );

    return $obErro;
}

/**
    * Apaga os dados do banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"                      );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaConciliacaoLancamentoContabil.class.php"   );
    $obTransacao      =  new Transacao;
    $obTTesourariaConciliacaoContabil =  new TTesourariaConciliacaoLancamentoContabil;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( !$obErro->ocorreu() ) {
            $obTTesourariaConciliacaoContabil->setDado( "cod_plano" , $this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getCodPlano());
            $obTTesourariaConciliacaoContabil->setDado( "exercicio" , $this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getExercicio());
            $obTTesourariaConciliacaoContabil->setDado( "mes",        $this->roRTesourariaConciliacao->getMes());
            $obErro = $obTTesourariaConciliacaoContabil->exclusao( $boTransacao );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaConciliacaoContabil );

    return $obErro;
}

}
