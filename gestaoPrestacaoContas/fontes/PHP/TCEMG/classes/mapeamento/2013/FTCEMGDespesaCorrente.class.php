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
    * Arquivo de mapeamento para a função que busca os dados de despesa corrente
    * Data de Criação   : 29/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGDespesaCorrente extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTCEMGDespesaCorrente()
{
    parent::Persistente();

    $this->setTabela('tcemg.fn_despesa_corrente');

    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);
    $this->AddCampo('periodo'       ,'integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "
    SELECT LPAD(periodo::VARCHAR,2,'0') AS periodo,
           LPAD(cod_tipo::VARCHAR,2,'0') AS cod_tipo,
           LPAD(despPesEncSoc::VARCHAR,16,'0') AS despPesEncSoc,
           LPAD(despJurDivInt::VARCHAR,16,'0') AS despJurDivInt,
           LPAD(despJurDivExt::VARCHAR,16,'0') AS despJurDivExt,
           LPAD(despOutDespCor::VARCHAR,16,'0') AS despOutDespCor

          FROM ".$this->getTabela()."( '".$this->getDado("exercicio")."'
                                     , '".$this->getDado("cod_entidade")."'
                                     , ".$this->getDado("periodo")."
                                     ) AS retorno(
                                                  periodo                  INTEGER,
                                                  cod_tipo                 TEXT,
                                                  despPesEncSoc            TEXT,
                                                  despJurDivInt            TEXT,
                                                  despJurDivExt            TEXT,
                                                  despOutDespCor           TEXT
                                                 )
          ORDER BY cod_tipo";

return $stSql;
}

}
