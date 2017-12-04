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
    * Página de Filtro de Despesas Mensais Fixas
    * Data de Criação : 04/09/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-11-16 20:37:16 -0200 (Qui, 16 Nov 2006) $

    * Casos de uso: uc-02.03.33
*/

/**

$Log$
Revision 1.7  2006/11/16 22:37:16  cleisson
Bug #7315#

Revision 1.6  2006/11/03 16:18:20  hboaventura
bug #7267#

Revision 1.5  2006/10/31 17:37:06  larocca
Bug #7207#

Revision 1.4  2006/10/16 16:32:21  larocca
Bug #7207#

Revision 1.3  2006/09/08 16:16:50  tonismar
alteração dos campos nro_identificacao e nro_contrato para num_identificacao e num_contrato

Revision 1.2  2006/09/08 10:18:08  tonismar
relatório de despesas fixas

Revision 1.1  2006/09/05 11:49:24  tonismar
desenvolvendo relatório de despesas fixas

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO );
include_once( CAM_FW_PDF."RRelatorio.class.php" );

class REmpenhoRelatorioDespesasMensaisFixas extends PersistenteRelatorio
{
    /**
        * @var Integer
        * @access Private
    */
    public $inCodEntidade;

    /**
        * @var String
        * @access Private
    */
    public $stExercicio;

    /**
        * @var Date
        * @access Private
    */
    public $dtPeriodicidadeInicial;

    /**
        * @var Date
        * @access Private
    */
    public $dtPeriodicidadeFinal;

    /**
        * @var Integer
        * @access Private
    */
    public $inCodTipo;

    /**
        * @var Integer
        * @access Private
    */
    public $inContrato;

    /**
        * @var Integer
        * @access Private
    */
    public $inCodLocal;

    /**
        * @var Integer
        * @access Private
    */
    public $inCodDotacao;

    /**
        * @var Integer
        * @access Private
    */
    public $inCodCredor;

    /**
        * @var Object
        * @access Private
    */
    public $obRRelatorio;

    /**
        * @var Object
        * @access Private
    */
    public $obTEmpenhoDespesasFixas;

    /* SETTERS */
    public function setCodEntidade($valor)
    {
        $this->inCodEntidade = $valor;
    }

    public function setExercicio($valor)
    {
        $this->stExercicio = $valor;
    }

    public function setPeriodicidadeInicial($valor)
    {
        $this->dtPeriodicidadeInicial = $valor;
    }

    public function setPeriodicidadeFinal($valor)
    {
        $this->dtPeriodicidadeFinal = $valor;
    }

    public function setCodTipo($valor)
    {
        $this->inCodTipo = $valor;
    }

    public function setContrato($valor)
    {
        $this->inContrato = $valor;
    }

    public function setCodLocal($valor)
    {
        $this->inCodLocal = $valor;
    }

    public function setCodDotacao($valor)
    {
        $this->inCodDotacao = $valor;
    }

    public function setCodCredor($valor)
    {
        $this->inCodCredor = $valor;
    }

    /* GETTERS */
    public function getCodEntidade()
    {
        return $this->inCodEntidade ;
    }

    public function getExercicio()
    {
        return $this->stExercicio ;
    }

    public function getPeriodicidadeInicial()
    {
        return $this->dtPeriodicidadeInicial ;
    }

    public function getPeriodicidadeFinal()
    {
        return $this->dtPeriodicidadeFinal ;
    }

    public function getCodTipo()
    {
        return $this->inCodTipo ;
    }

    public function getContrato()
    {
        return $this->inContrato ;
    }

    public function getCodLocal()
    {
        return $this->inCodLocal ;
    }

    public function getCodDotacao()
    {
        return $this->inCodDotacao ;
    }

    public function getCodCredor()
    {
        return $this->inCodCredor ;
    }

    /**
       * Method Constructor
       * @access Private
    */

    public function REmpenhoRelatorioDespesasMensaisFixas()
    {
        $this->obRRelatorio = new RRelatorio();
    }

    public function geraRelatorioDespesasMensaisFixas(&$arRecordSet, &$arRecordSet1)
    {
        $inCount = 0;

        include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoDespesasFixas.class.php" );
        $obTEmpenhoDespesasFixas = new TEmpenhoDespesasFixas();
        $obTEmpenhoDespesasFixas->setDado('exercicio', $this->getExercicio() );
        $obTEmpenhoDespesasFixas->setDado('cod_entidade', $this->inCodEntidade );
        $obTEmpenhoDespesasFixas->setDado('cod_tipo', $this->inCodTipo );
        if ($this->inContrato) {
            $obTEmpenhoDespesasFixas->setDado('nr_contrato', $this->inContrato );
        }
        if ($this->inCodLocal) {
            $obTEmpenhoDespesasFixas->setDado('cod_local', $this->inCodLocal);
        }
        if ($this->inCodDotacao) {
            $obTEmpenhoDespesasFixas->setDado('cod_despesa', $this->inCodDotacao);
        }
        if ($this->inCodCredor) {
            $obTEmpenhoDespesasFixas->setDado('numcgm', $this->inCodCredor);
        }
        $obTEmpenhoDespesasFixas->recuperaRelatorioDespesasMensaisFixas( $rsDespesa );

        while ( !$rsDespesa->eof() ) {

            $arDespesa[0]['coluna1'] = 'Entidade:';
            $arDespesa[0]['coluna2'] = $rsDespesa->getCampo('cod_entidade').' '.$rsDespesa->getCampo('nom_entidade');

            $arDespesa[1]['coluna1'] = 'Exercício:';
            $arDespesa[1]['coluna2'] = $rsDespesa->getCampo('exercicio');

            $arDespesa[2]['coluna1'] = 'Data de Inclusão:';
            $arDespesa[2]['coluna2'] = $rsDespesa->getCampo('dt_inclusao');

            $arDespesa[3]['coluna1'] = 'Tipo:';
            $arDespesa[3]['coluna2'] = $rsDespesa->getCampo('cod_tipo').' - '.$rsDespesa->getCampo('descricao');

            $arDespesa[4]['coluna1'] = 'Identificador:';
            $arDespesa[4]['coluna2'] = $rsDespesa->getCampo('num_identificacao');

            $arDespesa[5]['coluna1'] = 'Nr. Contrato:';
            $arDespesa[5]['coluna2'] = $rsDespesa->getCampo('num_contrato');

            $arDespesa[6]['coluna1'] = 'Dotação:';
            $arDespesa[6]['coluna2'] = $rsDespesa->getCampo('cod_despesa').' - '.$rsDespesa->getCampo('nom_despesa');

            $arDespesa[7]['coluna1'] = 'Credor:';
            $arDespesa[7]['coluna2'] = $rsDespesa->getCampo('numcgm').' - '.$rsDespesa->getCampo('nom_cgm');

            $arDespesa[8]['coluna1'] = 'Local:';
            $arDespesa[8]['coluna2'] = $rsDespesa->getCampo('cod_local').' - '.$rsDespesa->getCampo('nom_local');

            $arDespesa[9]['coluna1'] = 'Dia de Vencimento:';
            $arDespesa[9]['coluna2'] = $rsDespesa->getCampo('dia_vencimento');

            $arRecordSet[$inCount] = new RecordSet;
            $arRecordSet[$inCount]->preenche( $arDespesa );

            $obTEmpenhoDespesasFixas->setDado('cod_empenho', $rsDespesa->getCampo('cod_empenho') );
            $obTEmpenhoDespesasFixas->setDado('cod_entidade', $rsDespesa->getCampo('cod_entidade') );
            $obTEmpenhoDespesasFixas->setDado('exercicio', $rsDespesa->getCampo('exercicio') );
            $obTEmpenhoDespesasFixas->setDado('cod_despesa', $rsDespesa->getCampo('cod_despesa') );
            $obTEmpenhoDespesasFixas->setDado('cod_despesa_fixa', $rsDespesa->getCampo('cod_despesa_fixa') );
            $obTEmpenhoDespesasFixas->recuperaRelatorioDespesasMensaisFixasDetalhe( $rsDetalhe );
            $rsDetalhe->setPrimeiroElemento();
            $arTemp = array();
            $inAcc = 0;
            while ( !$rsDetalhe->eof() ) {
                $arTemp[$inAcc]['empenho'] = $rsDetalhe->getCampo('cod_empenho');
                $arTemp[$inAcc]['dt_empenho'] = $rsDetalhe->getCampo('dt_empenho');
                $arTemp[$inAcc]['empenhado'] = 'R$ '.number_format($rsDetalhe->getCampo('empenhado'),2,',','.');
                $arTemp[$inAcc]['liquidado'] = 'R$ '.number_format($rsDetalhe->getCampo('liquidado'),2,',','.');
                $arTemp[$inAcc]['pago'] = 'R$ '.number_format($rsDetalhe->getCampo('pago'),2,',','.');
                $arTemp[$inAcc]['pagar_liquidado'] = 'R$ '.number_format($rsDetalhe->getCampo('pagar_liquidado'),2,',','.');
                $rsDetalhe->proximo();
                $inAcc++;
            }
            if ( $rsDetalhe->getNumLinhas() > 0 ) {
                $arTotais = $rsDetalhe->getSomaCampo('empenhado,liquidado,pago,pagar_liquidado');
                $arTemp[$inAcc]['empenho'] = 'TOTAIS';
                $arTemp[$inAcc]['dt_empenho'] = '';
                $arTemp[$inAcc]['empenhado'] = 'R$ '.number_format($arTotais['empenhado'],2,',','.');
                $arTemp[$inAcc]['liquidado'] = 'R$ '.number_format($arTotais['liquidado'],2,',','.');
                $arTemp[$inAcc]['pago'] = 'R$ '.number_format($arTotais['pago'],2,',','.');
                $arTemp[$inAcc]['pagar_liquidado'] = 'R$ '.number_format($arTotais['pagar_liquidado'],2,',','.');
            }
            $rsRecordSet1 = new RecordSet();
            $rsRecordSet1->preenche($arTemp);

            $arRecordSet1[$inCount] = new RecordSet();
            $arRecordSet1[$inCount] = $rsRecordSet1;

            $rsDespesa->proximo();
            $inCount++;
        }

    }

}
