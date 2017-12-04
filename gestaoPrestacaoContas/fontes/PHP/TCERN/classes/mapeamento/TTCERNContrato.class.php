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
    * Extensão da Classe de mapeamento
    * Data de Criação: 2/02/2012

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCERNContrato extends Persistente
{

function TTCERNContrato()
{
    parent::Persistente();
    $this->setTabela('tcern.contrato');

    $this->setCampoCod('num_contrato');
    $this->setComplementoChave('num_convenio, cod_entidade, exercicio, exercicio_contrato');

    $this->AddCampo('num_contrato'                , 'integer', true, ''    , false, true);
    $this->AddCampo('exercicio_contrato'          , 'varchar', true, '4'   , false, true);
    $this->AddCampo('num_convenio'                , 'integer', true, ''    , false, false);
    $this->AddCampo('cod_entidade'                , 'integer', true, ''    , false, false);
    $this->AddCampo('exercicio'                   , 'varchar', true, '4'   , false, false);
    $this->AddCampo('cod_processo'                , 'integer', true, ''    , false, false);
    $this->AddCampo('exercicio_processo'          , 'varchar', true, '4'   , false, false);
    $this->AddCampo('bimestre'                    , 'integer', true, ''    , false, false);
    $this->AddCampo('cod_conta_especifica'        , 'varchar', true, '50'  , false, false);
    $this->AddCampo('dt_entrega_recurso'          , 'date'   , true, ''    , false, false);
    $this->AddCampo('valor_repasse'               , 'numeric', true, '14,2', false, false);
    $this->AddCampo('valor_executado'             , 'numeric', true, '14,2', false, false);
    $this->AddCampo('receita_aplicacao_financeira', 'numeric', true, '14,2', false, false);
    $this->AddCampo('dt_recebimento_saldo'        , 'date'   , true, ''    , false, false);
    $this->AddCampo('dt_prestacao_contas'         , 'date'   , true, ''    , false, false);
}

function recuperaContrato(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaContrato().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContrato()
{
    $stSql .= " SELECT *
                       , contrato.bimestre AS bimestre                                        \n
                       , contrato.cod_processo AS cod_processo                                \n
                       , contrato.exercicio_processo AS exercicio_processo                    \n
                  FROM tcern.contrato                                                         \n
            INNER JOIN tcern.convenio                                                         \n
                    ON convenio.num_convenio = contrato.num_convenio                          \n
                   AND convenio.cod_entidade = contrato.cod_entidade                          \n
                   AND convenio.exercicio    = contrato.exercicio                             \n
                 WHERE contrato.exercicio          = ".$this->getDado('exercicio')."          \n
                   AND contrato.cod_entidade       = ".$this->getDado('cod_entidade')."       \n
                   AND contrato.num_contrato       = ".$this->getDado('num_contrato')."       \n
                   AND contrato.num_convenio       = ".$this->getDado('num_convenio')."       \n
                   AND contrato.exercicio_contrato = ".$this->getDado('exercicio_contrato')." \n
            ";

    return $stSql;
}

function recuperaContratoConvenio(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaContratoConvenio().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratoConvenio()
{
    $stSql .= "SELECT   'N° Convênio: ' || convenio.num_convenio ||
                        ' - Entidade: ' || sw_cgm.nom_cgm ||
                        ' - Exercício: ' || convenio.exercicio ||
                        ' - N° Contrato: ' || contrato.num_contrato ||
                        ' - Exercício contrato: ' || contrato.exercicio_contrato AS contrato

                        , convenio.num_convenio || '§' ||
                          entidade.cod_entidade || '§' ||
                          contrato.exercicio || '§' ||
                          contrato.num_contrato || '§' ||
                          contrato.exercicio_contrato AS num_contrato

                  FROM tcern.convenio
            INNER JOIN tcern.contrato
                    ON contrato.num_convenio = convenio.num_convenio
                   AND contrato.cod_entidade = convenio.cod_entidade
                   AND contrato.exercicio    = convenio.exercicio
            INNER JOIN orcamento.entidade
                    ON entidade.cod_entidade = convenio.cod_entidade
                   AND entidade.exercicio    = convenio.exercicio
            INNER JOIN sw_cgm
                    ON sw_cgm.numcgm = entidade.numcgm ";

    return $stSql;
}

}
