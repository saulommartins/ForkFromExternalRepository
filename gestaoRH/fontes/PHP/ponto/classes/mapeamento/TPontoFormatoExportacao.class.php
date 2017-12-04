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
    * Classe de mapeamento da tabela ponto.formato_exportacao
    * Data de Criação: 21/10/2008

    * @author Analista     : Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoFormatoExportacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPontoFormatoExportacao()
{
    parent::Persistente();
    $this->setTabela("ponto.formato_exportacao");

    $this->setCampoCod('cod_formato');
    $this->setComplementoChave('');

    $this->AddCampo('cod_formato'    ,'sequence',true  ,''    ,true,false);
    $this->AddCampo('descricao'      ,'varchar' ,true  ,'60'  ,false,false);
    $this->AddCampo('formato_minutos','char'    ,true  ,'1'   ,false,false);

}

function exportarPonto(&$rsRecordset)
{
    $obErro = $this->executaRecupera("montaExportarPonto",$rsRecordset);

    return $obErro;
}

function montaExportarPonto()
{
    $stSql = "select * from exportarPonto(".$this->getDado("cod_formato")."
                                        ,'".$this->getDado("dt_inicial")."'
                                        ,'".$this->getDado("dt_final")."'
                                        ,'".Sessao::getEntidade()."'
                                        ,'".$this->getDado("filtro")."'
                                        ,'".$this->getDado("codigos")."'
                                        ,".Sessao::getExercicio().");";

    return $stSql;
}

function servidoresExportador(&$rsRecordset)
{
    $obErro = $this->executaRecupera("montaServidoresExportador",$rsRecordset);

    return $obErro;
}

function montaServidoresExportador()
{
    $stSql = "select count(1) as contador_contratos from (select 1 from ponto.exportacao_ponto group by cod_contrato) as tabela";

    return $stSql;
}

}
?>
