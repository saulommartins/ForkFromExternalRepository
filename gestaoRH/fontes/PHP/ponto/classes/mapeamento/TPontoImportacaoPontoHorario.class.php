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
    * Classe de mapeamento da tabela ponto.importacao_ponto_horario
    * Data de Criação: 09/10/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.10.04

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoImportacaoPontoHorario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPontoImportacaoPontoHorario()
{
    parent::Persistente();
    $this->setTabela("ponto.importacao_ponto_horario");

    $this->setCampoCod('cod_hora');
    $this->setComplementoChave('cod_contrato,cod_ponto');

    $this->AddCampo('cod_contrato'   ,'integer' ,true  ,'',true,'TPontoImportacaoPonto');
    $this->AddCampo('cod_ponto'      ,'integer' ,true  ,'',true,'TPontoImportacaoPonto');
    $this->AddCampo('cod_hora'       ,'sequence',true  ,'',true,false);
    $this->AddCampo('horario'        ,'time'    ,true  ,'',false,false);
    $this->AddCampo('cod_importacao' ,'integer' ,true  ,'',false,false);
}

function montaRecuperaRelacionamento()
{
    $stSql .= "      SELECT importacao_ponto_horario.*                                             \n";
    $stSql .= "       FROM ponto.importacao_ponto                                                  \n";
    $stSql .= " INNER JOIN ponto.importacao_ponto_horario                                          \n";
    $stSql .= "         ON importacao_ponto.cod_contrato = importacao_ponto_horario.cod_contrato   \n";
    $stSql .= "        AND importacao_ponto.cod_ponto = importacao_ponto_horario.cod_ponto         \n";

    return $stSql;
}

}
?>
