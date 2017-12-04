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
    * Classe de Regra de Negócio para Saldo da Tesouraria
    * Data de Criação   : 17/02/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2008-03-20 12:09:41 -0300 (Qui, 20 Mar 2008) $

    * Casos de uso: uc-02.04.22
*/

/*
$Log$
Revision 1.7  2007/07/24 20:44:34  hboaventura
Bug#9478#

Revision 1.6  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" );

/**
    * Classe de Regra de Saldo de Tesouraria
    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RTesourariaSaldoTesouraria
{
/*
    * @var Object
    * @access Private
*/
var $obRContabilidadePlanoBanco;
/*
    * @var Numeric
    * @access Private
*/
var $nuVlSaldo;

/*
    * @access Public
    * @param Object $valor
*/
function setRContabilidadePlanoBanco($valor) { $this->obRContabilidadePlanoBanco = $valor; }
/*
    * @access Public
    * @param Numeric $valor
*/
function setVlSaldo($valor) { $this->nuVlSaldo                  = $valor; }

/*
    * @access Public
    * @return Object
*/
function getRContabilidadePlanoBanco() { return $this->obRContabilidadePlanoBanco; }
/*
    * @access Public
    * @return Numeric
*/
function getVlSaldo() { return $this->nuVlSaldo;                  }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaSaldoTesouraria()
{
    $this->obRContabilidadePlanoBanco = new RContabilidadePlanoBanco();
}

/**
    * Método para salvar dados no banco
    * @access Public
    * @param Object $boTransacao Parâmetro de Transação
    * @return Object $obErro Objeto de Erro
*/
function salvar($boTransacao = "")
{
    $obErro = $this->listar( $rsSaldo, '', '', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obRContabilidadePlanoBanco->listarContasBancos($rsRecordBanco, $boTransacao);

        if ($rsRecordBanco->getNumLinhas()<0) {
            $obErro->setDescricao("Entidade informa é diferente da Entidade da Conta Banco!");
        } else {
            if ( !$obErro->ocorreu() ) {
                if ( $rsSaldo->eof() ) {
                    $obErro = $this->incluir( $boTransacao );
                } else {
                    $obErro = $this->alterar( $boTransacao );
                }
            }
        }
    }

    return $obErro;
}

/**
    * Inclui os dados no banco de dados
    * @access Private
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                      );
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaSaldoTesouraria.class.php" );
    $obTransacao                  = new Transacao();
    $obTTesourariaSaldoTesouraria = new TTesourariaSaldoTesouraria();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

 if ( !$obErro->ocorreu() ) {

        $obTTesourariaSaldoTesouraria->setDado( "exercicio", $this->obRContabilidadePlanoBanco->getExercicio() );
        $obTTesourariaSaldoTesouraria->setDado( "cod_plano", $this->obRContabilidadePlanoBanco->getCodPlano()  );
           $obTTesourariaSaldoTesouraria->setDado( "vl_saldo" , $this->nuVlSaldo );
           $obErro = $obTTesourariaSaldoTesouraria->inclusao( $boTransacao );

    }

    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaSaldoTesouraria );

    return $obErro;
}

/**
    * Altera os dados no banco de dados
    * @access Private
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                      );
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaSaldoTesouraria.class.php" );
    $obTransacao                  = new Transacao();
    $obTTesourariaSaldoTesouraria = new TTesourariaSaldoTesouraria();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTTesourariaSaldoTesouraria->setDado( "exercicio", $this->obRContabilidadePlanoBanco->getExercicio() );
        $obTTesourariaSaldoTesouraria->setDado( "cod_plano", $this->obRContabilidadePlanoBanco->getCodPlano()  );
           $obTTesourariaSaldoTesouraria->setDado( "vl_saldo" , $this->nuVlSaldo                                  );
        $obErro = $obTTesourariaSaldoTesouraria->alteracao( $boTransacao );

    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaSaldoTesouraria );

    return $obErro;
}

/**
    * Deleta os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                      );
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaSaldoTesouraria.class.php" );
    $obTransacao                  = new Transacao();
    $obTTesourariaSaldoTesouraria = new TTesourariaSaldoTesouraria();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTTesourariaSaldoTesouraria->setDado( "exercicio", $this->obRContabilidadePlanoBanco->getExercicio() );
        $obTTesourariaSaldoTesouraria->setDado( "cod_plano", $this->obRContabilidadePlanoBanco->getCodPlano()  );
        $obErro = $obTTesourariaSaldoTesouraria->exclusao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaSaldoTesouraria );

    return $obErro;
}

/**
    * Deleta os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                      );
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaSaldoTesouraria.class.php" );
    $obTransacao                  = new Transacao();
    $obTTesourariaSaldoTesouraria = new TTesourariaSaldoTesouraria();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTTesourariaSaldoTesouraria->setDado( "exercicio", $this->obRContabilidadePlanoBanco->getExercicio() );
        $obTTesourariaSaldoTesouraria->setDado( "cod_plano", $this->obRContabilidadePlanoBanco->getCodPlano()  );
        $obErro = $obTTesourariaSaldoTesouraria->recuperaPorChave( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setVlSaldo( $rsRecordSet->getCampo("vl_saldo") );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaSaldoTesouraria );

    return $obErro;
}

/**
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaSaldoTesouraria.class.php" );
    $obTTesourariaSaldoTesouraria = new TTesourariaSaldoTesouraria();
    if( $this->obRContabilidadePlanoBanco->getExercicio() )
        $stFiltro .= " exercicio = '".$this->obRContabilidadePlanoBanco->getExercicio()."' AND ";
    if( $this->obRContabilidadePlanoBanco->getCodPlano() )
        $stFiltro .= " cod_plano = ".$this->obRContabilidadePlanoBanco->getCodPlano()." AND ";

    $stFiltro = ( $stFiltro ) ? " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $obErro = $obTTesourariaSaldoTesouraria->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Método para recuperar saldo total da tesouraria
    * @access Public
    * @param Numeric $nuVlSaldo   Valor do saldo da tesouraria
    * @param String  $stDtInicial Data inicial para contabilizar o saldo
    * @param String  $stDtFinal   Data final para contabilizar o saldo
    * @param Object  $boTransacao Parâmetro de Transação
    * @return Object $obErro Objeto de Erro
*/
function consultarSaldoTesouraria(&$nuVlSaldo, $stDtInicial = "", $stDtFinal = "", $boTransacao = "")
{
    include_once( CAM_GF_TES_MAPEAMENTO."FTesourariaSaldoContaTesouraria.class.php" );
    $obFTesourariaSaldoContaTesouraria = new FTesourariaSaldoContaTesouraria();

    $stDtInicial = ( $stDtInicial ) ? $stDtInicial : "01/01/".$this->obRContabilidadePlanoBanco->getExercicio();
    $stDtFinal   = ( $stDtFinal   ) ? $stDtFinal   : "31/12/".$this->obRContabilidadePlanoBanco->getExercicio();

    $obFTesourariaSaldoContaTesouraria->setDado( "exercicio" , $this->obRContabilidadePlanoBanco->getExercicio() );
    $obFTesourariaSaldoContaTesouraria->setDado( "cod_plano" , $this->obRContabilidadePlanoBanco->getCodPlano()  );
    $obFTesourariaSaldoContaTesouraria->setDado( "dt_inicial", $stDtInicial );
    $obFTesourariaSaldoContaTesouraria->setDado( "dt_final"  , $stDtFinal   );
    $obErro = $obFTesourariaSaldoContaTesouraria->recuperaRelacionamento( $rsSaldo, '', '', $boTransacao );
    $nuVlSaldo = $rsSaldo->getCampo( "valor" );

    return $obErro;
}

/**
    * Método para gerar saldo das contas bancos para o exercicio seguinte
    * @access Public
    * @param Object $boTransacao Parâmetro de Transação
    * @return Object $obErro Objeto de Erro
*/
function gerarSaldoExercicioSeguinte($boTransacao = "")
{
    return $obErro;
}

}
