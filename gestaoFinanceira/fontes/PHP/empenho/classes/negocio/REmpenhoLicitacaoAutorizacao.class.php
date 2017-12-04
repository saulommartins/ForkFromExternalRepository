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
    * Classe de Regra de Autorizacao por licitação
    * Data de Criação   : 25/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.15
                    uc-02.03.02
*/

/*
$Log$
Revision 1.7  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"          );
include_once ( CAM_FW_BANCO_DADOS."TransacaoSIAM.class.php"      );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );

/**
    * Classe de Regra de Autorização por licitação
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class REmpenhoLicitacaoAutorizacao
{
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $obTransacaoSIAM;
/**
    * @access Private
    * @var Object
*/
var $obTSamlinkSiamAutoriza;
/**
    * @access Private
    * @var Object
*/
var $roUltimaAutorizacao;
/**
    * @access Private
    * @var Array
*/
var $arAutorizacao;

/**
    * @access Public
    * @param Array $Valor
*/
function setAutorizacao($valor) { $this->arAutorizacao = $valor; }

/**
    * @access Public
    * @return Array
*/
function getAutorizacao() { return $this->arAutorizacao; }

/**
     * Método construtor
     * @access Public
*/
function REmpenhoLicitacaoAutorizacao()
{
    $this->obTransacao            = new Transacao;
    $this->obTransacaoSIAM        = new TransacaoSIAM;
}

/**
    * Método para adicionar um Objeto Autorizacao ao array
    * @access Public
*/
function addAutorizacao()
{
    $this->arAutorizacao[]     = new REmpenhoAutorizacaoEmpenho();
    $this->roUltimaAutorizacao = &$this->arAutorizacao[ count( $this->arAutorizacao ) -1 ];
}

/**
    * Método para incluir dados na tabela autoriza do sam30
    * @access Private
    * @param Object $obREmpenhoAutorizacaoEmpenho
    * @param Object $boTransacao
    * @return Object #obErro
*/
function incluirAutorizacaoSam30($obREmpenhoAutorizacaoEmpenho, $boTransacaoSIAM = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoSamlinkSiamAutoriza.class.php"  );
    $obTSamlinkSiamAutoriza = new TSamlinkSiamAutoriza;

    $obTSamlinkSiamAutoriza->setDado( 'e04_autori', $obREmpenhoAutorizacaoEmpenho->getCodAutorizacao()                  );
    $obTSamlinkSiamAutoriza->setDado( 'e04_tipol' , $obREmpenhoAutorizacaoEmpenho->getTipoLicitacao()                   );
    $obTSamlinkSiamAutoriza->setDado( 'e04_numerl', $obREmpenhoAutorizacaoEmpenho->getNumLicitacao()                    );
    $obTSamlinkSiamAutoriza->setDado( 'e04_numcgm', $obREmpenhoAutorizacaoEmpenho->obRCGM->getNumCGM()                  );
    $obTSamlinkSiamAutoriza->setDado( 'e04_dotac' , $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->getCodDespesa() );
    $obTSamlinkSiamAutoriza->setDado( 'e04_emiss' , date( 'd/m/Y' )                                                     );
    $obErro = $obTSamlinkSiamAutoriza->inclusao( $boTransacaoSIAM );

    return $obErro;
}

/**
    * Incluir Ordem Pagamento
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    //ABRE TRANSACAO COM O SIAM
    $boFlagTransacaoSIAM = false;
    $obErro = $this->obTransacaoSIAM->abreTransacao( $boFlagTransacaoSIAM, $boTransacaoSIAM );
    if ( !$obErro->ocorreu() ) {
        //ABRE TRANSACAO COM O URBEM
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( count( $this->arAutorizacao ) > 0 ) {
                for ( $x = 0; $x < count( $this->arAutorizacao ); $x++ ) {

                    $obErro = $this->arAutorizacao[$x]->incluir( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->incluirAutorizacaoSam30( $this->arAutorizacao[$x], $boTransacaoSIAM );
                    }

                    if( $obErro->ocorreu() )
                        break;
                }
            }
        }
    }
    $this->obTransacaoSIAM->fechaTransacao( $boFlagTransacaoSIAM, $boTransacaoSIAM, $obErro );
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

}
