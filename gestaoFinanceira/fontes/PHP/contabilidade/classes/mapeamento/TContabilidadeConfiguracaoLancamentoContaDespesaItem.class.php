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
    * Classe de mapeamento da tabela CONTABILIDADE.CONFIGURACAO_LANCAMENTO_DEBITO
    * Data de Criação: 24/10/2011

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: Davi Aroldi

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.03.03
                    uc-02.02.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TContabilidadeConfiguracaoLancamentoContaDespesaItem extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TContabilidadeConfiguracaoLancamentoContaDespesaItem()
    {
        parent::Persistente();
        $this->setTabela('contabilidade.configuracao_lancamento_conta_despesa_item');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_conta_despesa,cod_item');

        $this->AddCampo('cod_item','integer',true,'',true,true);
        $this->AddCampo('exercicio','char',true,'04',true,true);
        $this->AddCampo('cod_conta_despesa','integer',true,'',true,true);
    }

    /**
        * Método Retorna Record Set
        * @access Public
    */
    public function consultarItem($boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $boOk        = true;
        $stSql = $this->montaConsultarCentro();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( !$rsRecordSet->eof() ) {
                foreach ($this->arEstrutura AS $obCampo) {
                    $this->setDado( $obCampo->getNomeCampo(), $rsRecordSet->getCampo( $obCampo->getNomeCampo() ) );
                }
            } else {
                $boOk = false;
            }
        } else {
            $boOk = false;
        }
        $this->setDebug($stSql);

        return $boOk;
    }

    /**
        * Método Retorna Sql Montado
        * @access Public
    */
    public function montaConsultarCentro()
    {
        $stSql = " SELECT cod_item
                        , exercicio
                        , cod_conta_despesa
                     FROM ".$this->getTabela()."
                    WHERE 1 = 1";
        if ($this->getDado('exercicio')) {
            $stSql .= " AND exercicio = '".$this->getDado('exercicio')."' ";
        }

        if ($this->getDado('cod_conta_despesa')) {
            $stSql .= " AND cod_conta_despesa = ".$this->getDado('cod_conta_despesa')." ";
        }

        if ($this->getDado('cod_item')) {
            $stSql .= " AND cod_item = ".$this->getDado('cod_item')." ";
        }

        return $stSql;
    }

    public function salvar()
    {
        $inCodContaDespesa = $this->getDado('cod_conta_despesa');
        $inCodItem = $this->getDado('cod_item');
        $stExercicio = $this->getDado('exercicio');
        $boOk = $this->consultarItem();

        $this->setDado('cod_conta_despesa', $inCodContaDespesa);
        $this->setDado('cod_item', $inCodItem);
        $this->setDado('exercicio', $stExercicio);

        if (!$boOk) { //se $boOk == false não possui registro referente a estas chaves
            if (!empty($inCodItem)) {
                $this->inclusao();
            }
        } else {
            if (empty($inCodItem)) {
                $this->exclusao();
            } else {
                $this->alteracao();
            }
        }

    }
}
?>
