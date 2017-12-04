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
  * Mapeamento da tabela frota.veiculo
  * Data de criação : 15/03/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Programador: Diego Barbosa Victoria

    $Id: TFrotaMarca.class.php 30088 2008-06-02 17:52:21Z diogo.zarpelon $
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( TCGM."TCGMPessoaJuridica.class.php" );

class TFrotaPosto extends TCGMPessoaJuridica
{
/**
    * Método Construtor
    * @access Private
*/

function TFrotaPosto()
{
    parent::Persistente();
    $this->setTabela('frota.posto');
    $this->setCampoCod('cgm_posto');
    $this->setComplementoChave('');
    $this->AddCampo('cgm_posto' ,'integer',true,'',true,true);
    $this->AddCampo('interno'   ,'boolean',false,'',false,false);
    $this->AddCampo('ativo'     ,'boolean',false,'',false,false);
}

function montaRecuperaRelacionamento()
{
$stSql .= "SELECT
                CGM.*,
                PJ.*,
                PO.*,
                CASE WHEN PO.ativo  = true THEN 'Sim' ELSE 'Não' END AS ativo_label,
                CASE WHEN PO.interno= true THEN 'Sim' ELSE 'Não' END AS interno_label
            FROM
                sw_cgm                   AS CGM,
                sw_cgm_pessoa_juridica   AS PJ,
                frota.posto              AS PO
            WHERE   CGM.numcgm = PJ.numcgm
            AND     CGM.numcgm <> 0
            AND     PJ.numcgm = PO.cgm_posto
";

    return $stSql;
}

function recuperaVinculoPosto(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaVinculoPosto",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaVinculoPosto()
{
    $stSQL = "Select *
                from frota.posto
               INNER JOIN frota.autorizacao
                  ON (autorizacao.cgm_fornecedor = posto.cgm_posto)";

    $stSQL.=" where not exists(select 1
                                 from frota.efetivacao
                                where autorizacao.cod_autorizacao = efetivacao.cod_autorizacao
                                  and autorizacao.exercicio = efetivacao.exercicio_autorizacao
                                )";

    if ($this->getDado('cgm_posto')) {
        $stSQL.="and posto.cgm_posto = ".$this->getDado('cgm_posto');
    }

    return $stSQL;
}

}
