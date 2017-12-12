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
* Classe de mapeamento para administracao.cep_logradouro
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 4979 $
$Name$
$Author: dibueno $
$Date: 2006-01-12 11:24:25 -0200 (Qui, 12 Jan 2006) $

Casos de uso: uc-01.03.98
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
class TCEPLogradouro extends Persistente
{
function TCEPLogradouro()
{
    parent::Persistente();
    $this->setTabela('sw_cep_logradouro');
    $this->setComplementoChave('cep, cod_logradouro');

    $this->AddCampo('cep',            'varchar', true,  8, true,  true);
    $this->AddCampo('cod_logradouro', 'integer', true, '', true,  true);
    $this->AddCampo('num_inicial',    'varchar', true,  6, false, false);
    $this->AddCampo('num_final',      'varchar', true,  6, false, false);
    $this->AddCampo('par',            'boolean', false, '',false,false);
    $this->AddCampo('impar',          'boolean', false, '',false,false);
}

function recuperaTodosNumeracao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao= "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaTodosNumeracao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaTodosNumeracao()
{
   $stSQL  = "SELECT                               \n";
   $stSQL .= "    *,                               \n";
   $stSQL .= "     CASE                            \n";
   $stSQL .= "        WHEN par AND impar THEN      \n";
   $stSQL .= "         'Todos'                     \n";
   $stSQL .= "        WHEN par THEN                \n";
   $stSQL .= "         'Pares'                     \n";
   $stSQL .= "        WHEN impar THEN              \n";
   $stSQL .= "         'Ímpares'                   \n";
   $stSQL .= "     END as numeracao                \n";
   $stSQL .= " FROM                                \n";
   $stSQL .= "     sw_cep_logradouro               \n";

    return $stSQL;
}

function montaRecuperaRelacionamento()
{
    $stSQL  = " SELECT                     \n";
    $stSQL .= "    C.*,                    \n";
    $stSQL .= "    CASE WHEN CL.CEP IS NULL THEN false ELSE true END as valido, \n";
    $stSQL .= "    CASE WHEN par AND impar THEN 'Todos' ELSE \n";
    $stSQL .= "        CASE WHEN par THEN 'Pares' ELSE \n";
    $stSQL .= "        'Ímpares' END       \n";
    $stSQL .= "    END as numeracao,       \n";
    $stSQL .= "    CL.cod_logradouro,      \n";
    $stSQL .= "    CL.num_inicial,         \n";
    $stSQL .= "    CL.num_final,           \n";
    $stSQL .= "    Cl.par,                 \n";
    $stSQL .= "    CL.impar                \n";
    $stSQL .= "FROM                        \n";
    $stSQL .= "    SW_CEP AS C             \n";
    $stSQL .= "LEFT JOIN                   \n";
    $stSQL .= "    SW_CEP_LOGRADOURO AS CL \n";
    $stSQL .= "ON                          \n";
    $stSQL .= "    C.CEP = CL.CEP          \n";

    return $stSQL;
}

function recuperaRelacionamentoCGMLogradouro(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao= "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoCGMLogradouro().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoCGMLogradouro()
{
     $stSQL  = " SELECT *
                   FROM sw_cgm_logradouro ";
     return $stSQL;
}

}
