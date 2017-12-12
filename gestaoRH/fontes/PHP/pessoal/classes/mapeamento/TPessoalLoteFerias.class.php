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
    * Classe de mapeamento da tabela pessoal.lote_ferias
    * Data de Criação: 22/02/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-tabelas

    $Id: TPessoalLoteFerias.class.php 64319 2016-01-15 13:51:29Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TPessoalLoteFerias extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela("pessoal.lote_ferias");

    $this->setCampoCod('cod_lote');
    $this->setComplementoChave('');

    $this->AddCampo('cod_lote'            ,'sequence',true  ,''     ,true,false);
    $this->AddCampo('nome'                ,'varchar' ,true  ,'200'  ,false,false);
    $this->AddCampo('mes_competencia'     ,'varchar' ,true  ,'2'    ,false,false);
    $this->AddCampo('ano_competencia'     ,'varchar' ,true  ,'4'    ,false,false);
}

function montaRecuperaRelacionamento()
{
    $stSql = " SELECT lote_ferias.*
                    , CASE WHEN coalesce( ( SELECT true FROM pessoal.lote_ferias_funcao WHERE cod_lote = lote_ferias.cod_lote LIMIT 1 ),false )
                                THEN 'F'
                           WHEN coalesce( ( SELECT true FROM pessoal.lote_ferias_local WHERE cod_lote = lote_ferias.cod_lote LIMIT 1 ),false )
                                THEN 'L'
                           WHEN coalesce( ( SELECT true FROM pessoal.lote_ferias_orgao WHERE cod_lote = lote_ferias.cod_lote LIMIT 1 ),false )
                                THEN 'O'
                           WHEN position( 'geral' in lower(nome)) > 0
                                THEN 'G'
                                ELSE 'C'
                      END as tipo_filtro
                 FROM pessoal.lote_ferias ";

    return $stSql;
}

}
?>
