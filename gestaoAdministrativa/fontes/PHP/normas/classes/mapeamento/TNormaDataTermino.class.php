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
* Classe de Mapeamento para tabela norma_data_termino
* Data de Criação: 17/01/2006

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Lizandro Kirst da Silva

$Revision: 19228 $
$Name$
$Author: rodrigo $
$Date: 2007-01-10 16:31:07 -0200 (Qua, 10 Jan 2007) $

Casos de uso: uc-03.05.09,
              uc-01.04.02
*/

/*
$Log$
Revision 1.5  2007/01/10 18:31:07  rodrigo
Adicionado o caso de uso uc-01.04.02 referente ao bug #8026#

Revision 1.4  2007/01/04 16:17:57  hboaventura
Bug #7899#, #7400#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
 * Efetua conexão com a tabela norma_data_termino
 * Data de Criação: 26/05/2004

 * @author Analista: Cassiano de Vasconcellos Ferreira
 * @author Desenvolvedor: Lizandro Kirst da Silva

*/
class TNormaDataTermino extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TNormaDataTermino()
{
    parent::Persistente();
    $this->setTabela('normas.norma_data_termino');

    $this->setCampoCod('cod_norma');

    $this->AddCampo('cod_norma'     ,'integer',true,'',true,false);
    $this->AddCampo('dt_termino'    ,'date',true,'',false,true);
}

function recuperaNormaDataTermino(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaNormaDataTermino();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaNormaDataTermino()
{
    $stSql = "  SELECT to_char(dt_termino,'dd/mm/yyyy') as dt_termino               \r\n";
    $stSql.= "    FROM normas.norma_data_termino                                    \r\n";
    $stSql.= "   WHERE cod_norma  =  ". $this->getDado('cod_norma') ."              \r\n";

    return $stSql;
}

}
