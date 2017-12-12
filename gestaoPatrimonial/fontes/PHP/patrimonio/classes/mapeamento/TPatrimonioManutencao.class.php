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
    * Data de Criação: 13/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 26149 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-10-17 11:28:05 -0200 (Qua, 17 Out 2007) $

    * Casos de uso: uc-03.01.07
*/

/*
$Log$
Revision 1.2  2007/10/17 13:26:42  hboaventura
correção dos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPatrimonioManutencao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPatrimonioManutencao()
{
    parent::Persistente();
    $this->setTabela('patrimonio.manutencao');
    $this->setCampoCod('');
    $this->setComplementoChave('cod_bem,dt_agendamento');
    $this->AddCampo('cod_bem','integer',true,'',true,true);
    $this->AddCampo('dt_agendamento','date',true,'',true,false);
    $this->AddCampo('numcgm','integer',true,'',false,true);
    $this->AddCampo('dt_garantia','date',false,'',false,false);
    $this->AddCampo('dt_realizacao','date',false,'',false,false);
    $this->AddCampo('observacao','varchar',false,'200',false,false);

}

    public function recuperaDadosBem(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaDadosBem",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDadosBem()
    {
        $stSql = "
            SELECT  bem.cod_bem
                 ,  bem.cod_natureza
                 ,  natureza.nom_natureza
                 ,  bem.cod_grupo
                 ,  grupo.nom_grupo
                 ,  bem.cod_especie
                 ,  especie.nom_especie
                 ,  bem.num_placa
                 ,  bem.descricao
              FROM  patrimonio.bem
        INNER JOIN  patrimonio.natureza
                ON  natureza.cod_natureza = bem.cod_natureza
        INNER JOIN  patrimonio.grupo
                ON  grupo.cod_natureza = bem.cod_natureza
               AND  grupo.cod_grupo = bem.cod_grupo
        INNER JOIN  patrimonio.especie
                ON  especie.cod_natureza = bem.cod_natureza
               AND  especie.cod_grupo = bem.cod_grupo
               AND  especie.cod_especie = bem.cod_especie
             WHERE ";
        if ( $this->getDado('cod_bem') ) {
            $stSql .= " bem.cod_bem = ".$this->getDado('cod_bem')." AND  ";
        }

        return substr($stSql,0,-6);
    }

    public function recuperaBensManutencao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaBensManutencao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaBensManutencao()
    {
        $stSql = "
            SELECT  bem.cod_bem
                 ,  bem.cod_natureza
                 ,  natureza.nom_natureza
                 ,  bem.cod_grupo
                 ,  grupo.nom_grupo
                 ,  bem.cod_especie
                 ,  especie.nom_especie
                 ,  bem.descricao
                 ,  bem.num_placa
                 ,  TO_CHAR(manutencao.dt_agendamento,'dd/mm/yyyy') AS dt_agendamento
                 ,  TO_CHAR(manutencao.dt_garantia,'dd/mm/yyyy') AS dt_garantia
                 ,  TO_CHAR(manutencao.dt_realizacao,'dd/mm/yyyy') AS dt_realizacao
                 ,  manutencao.observacao
                 ,  manutencao.numcgm
                 ,  sw_cgm.nom_cgm
              FROM  patrimonio.bem
        INNER JOIN  patrimonio.natureza
                ON  natureza.cod_natureza = bem.cod_natureza
        INNER JOIN  patrimonio.grupo
                ON  grupo.cod_natureza = bem.cod_natureza
               AND  grupo.cod_grupo = bem.cod_grupo
        INNER JOIN  patrimonio.especie
                ON  especie.cod_natureza = bem.cod_natureza
               AND  especie.cod_grupo = bem.cod_grupo
               AND  especie.cod_especie = bem.cod_especie
         LEFT JOIN  patrimonio.manutencao
                ON  manutencao.cod_bem = bem.cod_bem
        ";
        if ( $this->getDado('dt_agendamento') ) {
            $stSql .= " AND manutencao.dt_agendamento = '".$this->getDado('dt_agendamento')."' ";
        }
        $stSql .= "
         LEFT JOIN  sw_cgm
                ON  sw_cgm.numcgm = manutencao.numcgm
             WHERE ";
        if ( $this->getDado('cod_bem') ) {
            $stSql .= " bem.cod_bem = ".$this->getDado('cod_bem')." AND  ";
        }

        return substr($stSql,0,-6);
    }
}
