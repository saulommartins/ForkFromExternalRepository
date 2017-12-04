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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 06/03/2012

  * @author Analista: Gelson
  * @author Desenvolvedor: Jean Felipe da Silva

*/

class TTCERNUnidadeGestora extends Persistente
{
/**
* Método Construtor
* * @access Private
*/

    public function TTCERNUnidadeGestora()
    {
        parent::Persistente();
        $this->setTabela('tcern.unidade_gestora');
        $this->setCampoCod('id');

        $this->AddCampo('id', 'integer', true,  '', true, false);
        $this->AddCampo('cod_institucional', 'numeric', true,  2, false, false);
        $this->AddCampo('cgm_unidade',       'integer', true, '', false,  true);
        $this->AddCampo('personalidade',     'numeric', true,  1, false, false);
        $this->AddCampo('administracao',     'numeric', true,  1, false, false);
        $this->AddCampo('natureza',          'integer', true, '', false,  true);
        $this->AddCampo('cod_norma',         'integer', true, '', false,  true);
        $this->AddCampo('situacao',          'boolean', true, '', false, false);
        $this->AddCampo('exercicio',         'varchar', true,  4, false, false);
    }

    /*function recuperaPrefeitura(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="") {
        return $this->executaRecupera("montaRecuperaPrefeitura",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaPrefeitura()
    {
        $stSql = "SELECT entidade.cod_entidade AS cod_entidade

                        FROM administracao.configuracao
                        JOIN orcamento.entidade
                          ON entidade.exercicio = configuracao.exercicio
                         AND entidade.cod_entidade = configuracao.valor
                        JOIN ";

        return $stSql;
    }*/

    public function montaRecuperaRelacionamento()
    {
        $stSql .= "SELECT sw_cgm.nom_cgm AS nom_cgm
                        , sw_cgm.numcgm AS numcgm
                        , entidade.cod_entidade AS cod_entidade
                        /*, configuracao_entidade.valor AS cod_orgao*/
                        , unidade_gestora.id AS id
                        , unidade_gestora.cod_institucional AS cod_institucional
                        , unidade_gestora.personalidade AS personalidade
                        , unidade_gestora.administracao AS administracao
                        , natureza_juridica.cod_natureza AS cod_natureza
                        , CASE WHEN unidade_gestora.situacao = TRUE THEN 1
                             ELSE 2
                          END AS situacao
                        , unidade_gestora.cod_norma AS cod_norma

                        FROM administracao.configuracao
                        JOIN orcamento.entidade
                          ON entidade.exercicio = configuracao.exercicio
                         AND entidade.cod_entidade = configuracao.valor::INTEGER

                        /*JOIN administracao.configuracao_entidade
                          ON entidade.exercicio = configuracao_entidade.exercicio
                         AND configuracao_entidade.cod_entidade = entidade.cod_entidade
                         AND configuracao_entidade.cod_modulo = 49
                         AND configuracao_entidade.parametro = 'cod_orgao_tce'*/

                        JOIN sw_cgm
                          ON sw_cgm.numcgm = entidade.numcgm
                        LEFT JOIN tcern.unidade_gestora
                          ON sw_cgm.numcgm = unidade_gestora.cgm_unidade
                        LEFT JOIN tcern.natureza_juridica
                          ON natureza_juridica.cod_natureza = unidade_gestora.natureza
                        LEFT JOIN normas.norma
                          ON norma.cod_norma = unidade_gestora.cod_norma

                       WHERE entidade.exercicio = '".Sessao::getExercicio()."'
                         AND configuracao.parametro = 'cod_entidade_prefeitura'";

        return $stSql;
    }
}
