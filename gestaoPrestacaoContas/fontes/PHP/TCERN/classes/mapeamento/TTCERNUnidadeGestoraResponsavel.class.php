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

class TTCERNUnidadeGestoraResponsavel extends Persistente
{
/**
* Método Construtor
* * @access Private
*/

    public function TTCERNUnidadeGestoraResponsavel()
    {
        parent::Persistente();
        $this->setTabela('tcern.unidade_gestora_responsavel');
        $this->setCampoCod('id');

        $this->AddCampo('id',              'integer', true, '', true,  false);
        $this->AddCampo('id_unidade',      'integer', true, '', false,  true);
        $this->AddCampo('cgm_responsavel', 'integer', true, '', false,  true);
        $this->AddCampo('cargo',           'varchar', true, 30, false, false);
        $this->AddCampo('cod_funcao',      'numeric', true,  1, false,  true);
        $this->AddCampo('dt_inicio',          'date', true, '', false, false);
        $this->AddCampo('dt_fim',             'date', true, '', false, false);
    }

    public function recuperaId(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaId",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperId()
    {
        $stSql = "SELECT id
                    FROM tcern.unidade_gestora_responsavel
                    JOIN tcern.unidade_gestora
                      ON unidade_gestora.id = unidade_gestora_responsavel.id_unidade";

        return $stSql;
    }

    /*function montaRecuperaPorChave() {
        $stSql.= "SELECT  unidade_gestora_responsavel.id AS id
                        , sw_cgm.nom_cgm AS nom_cgm
                        , sw_cgm.numcgm AS numcgm
                        , unidade_gestora_responsavel.cargo AS cargo
                        , funcao_gestor.cod_funcao AS funcao
                        , to_char(unidade_gestora_responsavel.dt_inicio,'dd/mm/yyyy') AS dt_inicio
                        , to_char(unidade_gestora_responsavel.dt_fim,'dd/mm/yyyy') AS dt_fim

                    FROM tcern.unidade_gestora_responsavel
                    JOIN sw_cgm
                      ON sw_cgm.numcgm = unidade_gestora_responsavel.cgm_responsavel
                    JOIN tcern.funcao_gestor
                      ON funcao_gestor.cod_funcao = unidade_gestora_responsavel.cod_funcao";

        return $stSql;
    }*/

    public function montaRecuperaRelacionamento()
    {
        $stSql.= "SELECT  sw_cgm.nom_cgm AS nom_cgm
                        , sw_cgm.numcgm AS numcgm
                        , unidade_gestora_responsavel.cargo AS cargo
                        , funcao_gestor.cod_funcao AS cod_funcao
                        , funcao_gestor.descricao AS descricao
                        , to_char(unidade_gestora_responsavel.dt_inicio,'dd/mm/yyyy') AS dt_inicio
                        , to_char(unidade_gestora_responsavel.dt_fim,'dd/mm/yyyy') AS dt_fim
                        , unidade_gestora_responsavel.id AS id
                    FROM tcern.unidade_gestora_responsavel
                    JOIN tcern.unidade_gestora
                      ON unidade_gestora.id = unidade_gestora_responsavel.id_unidade
                    JOIN sw_cgm
                      ON sw_cgm.numcgm = unidade_gestora_responsavel.cgm_responsavel
                    JOIN tcern.funcao_gestor
                      ON funcao_gestor.cod_funcao = unidade_gestora_responsavel.cod_funcao

                    WHERE unidade_gestora.exercicio = '".Sessao::getExercicio()."'";

        return $stSql;
    }
}
