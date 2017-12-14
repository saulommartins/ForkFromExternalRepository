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
    * Classe de mapeamento da tabela TGO.CONFIGURACAO_LOA
    * Data de Criação: 21/01/2015

    * @author Analista: 
    * @author Desenvolvedor: Lisiane Morais

    * @package URBEM
    * @subpackage Mapeamento
    *
    * $Id: $
    *
    * $Name: $
    * $Date: $
    * $Author: $
    * $Rev: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TCMGOConfiguracaoLOA extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TCMGOConfiguracaoLOA()
    {
        parent::Persistente();
        $this->setTabela('tcmgo.configuracao_loa');

        $this->setCampoCod('exercicio');
        $this->setComplementoChave('');

        $this->AddCampo('exercicio'                               , 'varchar',  true,  4,  true, false);
        $this->AddCampo('cod_norma'                               , 'integer', false, '', false,  true);
        $this->AddCampo('percentual_suplementacao'                , 'numeric', false, '', false, false);
        $this->AddCampo('percentual_credito_interna'              , 'numeric', false, '', false, false);
        $this->AddCampo('percentual_credito_antecipacao_receita'  , 'numeric', false, '', false, false);

    }

    public function recuperaRegistro10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRegistro10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRegistro10()
    {
        $stSql = "
        SELECT
               10 AS tipo_registro
             , norma.num_norma AS num_loa
             , TO_CHAR(norma.dt_assinatura, 'ddmmyyyy') AS dt_loa
             , configuracao_loa.percentual_suplementacao AS perc_suplementacao
             , configuracao_loa.percentual_credito_interna AS perc_op_cred_int
             , configuracao_loa.percentual_credito_antecipacao_receita AS perc_op_cred_aro

          FROM  tcmgo.configuracao_loa

          JOIN  normas.norma
            ON  norma.cod_norma = configuracao_loa.cod_norma

         WHERE  configuracao_loa.exercicio = '".$this->getDado('exercicio')."'
        ";

        return $stSql;
    }

    public function recuperaRegistro11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRegistro11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRegistro11()
    {
        $stSql ="   SELECT
                              11 AS tipo_registro
                            , norma.num_norma AS num_loa
                            , CASE tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade
                                WHEN 6 THEN 1
                                WHEN 1 THEN 4
                                WHEN 2 THEN 4
                                WHEN 3 THEN 4
                                WHEN 4 THEN 4
                                WHEN 5 THEN 5
                                WHEN 7 THEN 5
                                WHEN 8 THEN 3
                                WHEN 9 THEN 9
                             END as meio_pub_loa
                            , tipo_veiculos_publicidade.descricao as desc_meio_loa
                            , TO_CHAR(norma.dt_publicacao,'ddmmyyyy') AS dt_lei_loa
                    
                    FROM  tcmgo.configuracao_loa

                    JOIN  normas.norma
                        ON  norma.cod_norma = configuracao_loa.cod_norma

                    LEFT JOIN ldo.homologacao
                        ON homologacao.cod_norma = norma.cod_norma

                    LEFT JOIN licitacao.veiculos_publicidade
                        ON veiculos_publicidade.numcgm = homologacao.numcgm_veiculo

                    LEFT JOIN licitacao.tipo_veiculos_publicidade
                        ON tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = veiculos_publicidade.cod_tipo_veiculos_publicidade

                    WHERE  configuracao_loa.exercicio = '".$this->getDado('exercicio')."'
                    
        GROUP BY   tipo_registro
                 , num_loa
                 , meio_pub_loa
                 , desc_meio_loa
                 , dt_lei_loa";

        return $stSql;
    }

    public function __destruct(){}

}

?>
