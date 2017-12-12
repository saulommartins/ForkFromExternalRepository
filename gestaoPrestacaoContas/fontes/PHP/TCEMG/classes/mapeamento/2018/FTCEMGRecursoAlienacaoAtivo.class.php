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
    * Arquivo de mapeamento para a função que busca os dados do recurso da alienacao do ativo
    * Data de Criação   : 29/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGRecursoAlienacaoAtivo extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FTCEMGRecursoAlienacaoAtivo()
    {
        parent::Persistente();

        $this->setTabela('tcemg.fn_recurso_alienacao_ativo');

        $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
        $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);
        $this->AddCampo('mes'           ,'integer',false,''    ,false,false);
        $this->AddCampo('dt_inicial'    ,'varchar',false,''    ,false,false);
        $this->AddCampo('dt_final'      ,'varchar',false,''    ,false,false);
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
                        SELECT 12 AS mes,
                               cod_vinculo,
                               cod_entidade AS cod_entidade,
                               empenhado_per as desp_emp,
                               liquidado_per as desp_liq,
                               pago_per as desp_paga,
                               rec_realizada as rec_realizada,
                               saldo_inicial as saldo_anterior

                          FROM tcemg.fn_recurso_alienacao_ativo('".$this->getDado('exercicio')."',
                                                                '".$this->getDado('cod_entidade')."',
                                                                '".$this->getDado('dt_inicial')."',
                                                                '".$this->getDado('dt_final')."'
                                                                ) AS retorno
                        ORDER BY cod_entidade
            ";
        return $stSql;
    }

}
