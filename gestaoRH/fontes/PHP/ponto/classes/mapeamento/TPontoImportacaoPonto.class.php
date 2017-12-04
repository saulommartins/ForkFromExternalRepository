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
    * Classe de mapeamento da tabela ponto.importacao_ponto
    * Data de Criação: 08/10/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.10.04

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoImportacaoPonto extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPontoImportacaoPonto()
{
    parent::Persistente();
    $this->setTabela("ponto.importacao_ponto");

    $this->setCampoCod('cod_ponto');
    $this->setComplementoChave('cod_contrato');

    $this->AddCampo('cod_ponto'      ,'sequence',true  ,'',true,false);
    $this->AddCampo('cod_contrato'   ,'integer' ,true  ,'',true,'TPessoalContrato');
    $this->AddCampo('cod_formato'    ,'integer' ,true  ,'',false,'TPontoFormatoImportacao');
    $this->AddCampo('dt_ponto'       ,'date'    ,true  ,'',false,false);
    $this->AddCampo('cod_importacao' ,'integer' ,true  ,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql = "";
    $stSql .= "    SELECT nom_cgm                                                                             \n";
    $stSql .= "         , cargo.descricao as funcao                                                           \n";
    $stSql .= "         , contrato.registro                                                                   \n";
    $stSql .= "      FROM ponto.importacao_ponto                                                              \n";
    $stSql .= "INNER JOIN pessoal.contrato                                                                    \n";
    $stSql .= "        ON importacao_ponto.cod_contrato = contrato.cod_contrato                               \n";
    $stSql .= "INNER JOIN pessoal.servidor_contrato_servidor                                                  \n";
    $stSql .= "        ON contrato.cod_contrato = servidor_contrato_servidor.cod_contrato                     \n";
    $stSql .= "INNER JOIN pessoal.servidor                                                                    \n";
    $stSql .= "        ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                     \n";
    $stSql .= "INNER JOIN sw_cgm                                                                              \n";
    $stSql .= "        ON servidor.numcgm = sw_cgm.numcgm                                                     \n";
    $stSql .= "INNER JOIN pessoal.contrato_servidor_funcao                                                    \n";
    $stSql .= "        ON contrato.cod_contrato = contrato_servidor_funcao.cod_contrato                       \n";
    $stSql .= "INNER JOIN ( SELECT cod_contrato                                                               \n";
    $stSql .= "                  , max(timestamp) as timestamp                                                \n";
    $stSql .= "               FROM pessoal.contrato_servidor_funcao                                           \n";
    $stSql .= "           GROUP BY cod_contrato) as max_contrato_servidor_funcao                              \n";
    $stSql .= "        ON contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato   \n";
    $stSql .= "       AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp         \n";
    $stSql .= "INNER JOIN pessoal.cargo                                                                       \n";
    $stSql .= "        ON contrato_servidor_funcao.cod_cargo = cargo.cod_cargo                                \n";

    return $stSql;
}

}
?>
