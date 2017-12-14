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
 * Classe de Regra de Negócio Configuracão Infração
 * Data de Criação   : 26/08/2008

 * @author Analista      : Heleno Menezes da Silva
 * @author Desenvolvedor : Fellipe Esteves dos Santos
 * @ignore

 * Casos de uso:
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO . "RConfiguracaoConfiguracao.class.php");

$stPrograma = "ConfiguracaoInfracao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList    	= "LS".$stPrograma.".php";
$pgForm 	= "FM".$stPrograma.".php";
$pgProc  	= "PR".$stPrograma.".php";
$pgOcul   	= "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

class RFISConfiguracaoInfracao extends RConfiguracaoConfiguracao
{
    public $inDocumentoNaoEntregue;
    public $inDocumentoEntregueForaPrazo;
    public $inDocumentoEntregueParcial;
    public $inPagamentoMenos;
    public $inDeclaracaoMenor;

    public function setDocumentoNaoEntregue($inDocumentoNaoEntregue)
    {
        $this->inDocumentoNaoEntregue = $inDocumentoNaoEntregue;
    }

    public function getDocumentoNaoEntregue()
    {
        return $this->inDocumentoNaoEntregue;
    }

    public function setDocumentoEntregueForaPrazo($inDocumentoEntregueForaPrazo)
    {
        $this->inDocumentoEntregueForaPrazo = $inDocumentoEntregueForaPrazo;
    }

    public function getDocumentoEntregueForaPrazo()
    {
        return $this->inDocumentoEntregueForaPrazo;
    }

    public function setDocumentoEntregueParcial($inDocumentoEntregueParcial)
    {
        $this->inDocumentoEntregueParcial = $inDocumentoEntregueParcial;
    }

    public function getDocumentoEntregueParcial()
    {
        return $this->inDocumentoEntregueParcial;
    }

    public function setPagamentoMenos($inPagamentoMenos)
    {
        $this->inPagamentoMenos = $inPagamentoMenos;
    }

    public function getPagamentoMenos()
    {
        return $this->inPagamentoMenos;
    }

    public function setDeclaracaoMenor($inDeclaracaoMenor)
    {
        $this->inDeclaracaoMenor = $inDeclaracaoMenor;
    }

    public function getDeclaracaoMenor()
    {
        return $this->inDeclaracaoMenor;
    }

    public function RFISConfiguracao()
    {
        include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
        include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php" );
        include_once( CLA_TRANSACAO );

        parent::RConfiguracaoConfiguracao();

        $this->obTConfiguracao  = new TAdministracaoConfiguracao;
        $this->obTransacao     	= new Transacao;
        $this->setExercicio          ( Sessao::getExercicio() );
        $this->setCodModulo     	( 34 );
    }

    public function consultar($boTransacao = "")
    {
            $this->obTConfiguracao->setDado( "cod_modulo", $this->getCodModulo()  	);
               $this->obTConfiguracao->setDado( "exercicio" , $this->getExercicio()          	);
            $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            /* documento não entregue */
               $this->obTConfiguracao->setDado( "parametro" , "documento_nao_entregue" );
            $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
            $this->setDocumentoNaoEntregue( $rsConfiguracao->getCampo( "valor" ) );

            /* documento entregue fora do prazo */
               $this->obTConfiguracao->setDado( "parametro" , "documento_entregue_fora_prazo" );
            $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
            $this->setDocumentoEntregueForaPrazo( $rsConfiguracao->getCampo( "valor" ) );

            /* documento entregue parcialmente */
               $this->obTConfiguracao->setDado( "parametro" , "documento_entregue_parcial" );
            $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
            $this->setDocumentoEntregueParcial( $rsConfiguracao->getCampo( "valor" ) );

            /* pagamento a menos */
               $this->obTConfiguracao->setDado( "parametro" , "pagamento_menos" );
            $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
            $this->setPagamentoMenos( $rsConfiguracao->getCampo( "valor" ) );

            /* declaração a menor */
               $this->obTConfiguracao->setDado( "parametro" , "declaracao_menor" );
            $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
            $this->setDeclaracaoMenor( $rsConfiguracao->getCampo( "valor" ) );
        }

        return $obErro;
    }

    public function configurar($param, $boTransacao = "")
    {
        global $pgForm;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            /* documento não entregue */
            $this->setParametro ('documento_nao_entregue');
            $this->setValor	  ($param['inDocumentoNaoEntregue']);
            $this->verificaParametro( $boExiste, $boTransacao );

            if ($boExiste) {
                parent::alterar( $boTransacao );
            } else {
                parent::incluir( $boTransacao );
            }

            /* documento entregue fora do prazo */
            $this->setParametro ('documento_entregue_fora_prazo');
            $this->setValor	  ($param['inDocumentoEntregueForaPrazo']);
            $this->verificaParametro( $boExiste, $boTransacao );

            if ($boExiste) {
                parent::alterar( $boTransacao );
            } else {
                parent::incluir( $boTransacao );
            }

            /* documento entregue parcialmente */
            $this->setParametro ('documento_entregue_parcial');
            $this->setValor	  ($param['inDocumentoEntregueParcial']);
            $this->verificaParametro( $boExiste, $boTransacao );

            if ($boExiste) {
                parent::alterar( $boTransacao );
            } else {
                parent::incluir( $boTransacao );
            }

            /* pagamento a menos */
            $this->setParametro ('pagamento_menos');
            $this->setValor	  ($param['inPagamentoMenos']);
            $this->verificaParametro( $boExiste, $boTransacao );

            if ($boExiste) {
                parent::alterar( $boTransacao );
            } else {
                parent::incluir( $boTransacao );
            }

            /* declaração a menor */
            $this->setParametro ('declaracao_menor');
            $this->setValor	  ($param['inDeclaracaoMenor']);
            $this->verificaParametro( $boExiste, $boTransacao );

            if ($boExiste) {
                parent::alterar( $boTransacao );
               } else {
                   parent::incluir( $boTransacao );
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        if (!$obErro->ocorreu()) {
            sistemaLegado::exibeAviso( "Efetuada com sucesso", "incluir", "aviso" );
        } else {
            sistemaLegado::exibeAviso( "Erro ao atualizar","n_incluir","erro" );
        }
    }
} // fecha classe

?>
