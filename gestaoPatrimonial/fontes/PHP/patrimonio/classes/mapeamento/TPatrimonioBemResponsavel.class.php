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
    * Data de Criação: 12/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 26769 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-11-13 17:39:04 -0200 (Ter, 13 Nov 2007) $
    $Id: TPatrimonioBemResponsavel.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPatrimonioBemResponsavel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPatrimonioBemResponsavel()
{
    parent::Persistente();
    $this->setTabela('patrimonio.bem_responsavel');
    $this->setCampoCod('cod_bem');
    $this->setComplementoChave('timestamp');

    $this->AddCampo('cod_bem','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);
    $this->AddCampo('numcgm','integer',true,'',false,true);
    $this->AddCampo('dt_inicio','date',false,'',false,false);
    $this->AddCampo('dt_fim','date',false,'',false,false);

}

    public function recuperaUltimoResponsavel(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaUltimoResponsavel",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaUltimoResponsavel()
    {
        $stSql = "
            SELECT bem_responsavel.cod_bem
                 , bem_responsavel.numcgm
                 , TO_CHAR(bem_responsavel.dt_inicio,'dd/mm/yyyy') as dt_inicio
                 , bem_responsavel.timestamp
              FROM patrimonio.bem_responsavel
             WHERE ";
        if ( $this->getDado( 'cod_bem' ) ) {
            $stSql.= " bem_responsavel.cod_bem = ".$this->getDado( 'cod_bem' )."  AND ";
        }
        if ( $this->getDado( 'dt_inicio' ) ) {
            $stSql.= " bem_responsavel.dt_inicio = TO_DATE('".$this->getDado( 'dt_inicio' )."','dd/mm/yyyy')  AND ";
        }
        if ( $this->getDado( 'numcgm' ) ) {
            $stSql.= " bem_responsavel.numcgm = ".$this->getDado( 'numcgm' )."  AND ";
        }

        $stSql = substr($stSql,0,-4);

        $stSql.= "
          ORDER BY timestamp DESC
             LIMIT 1
        ";

        return $stSql;
    }

    public function verificaResponsavelBem(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaVerificaResponsavelBem",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaVerificaResponsavelBem()
    {
        $stSql= "
            SELECT dt_fim
              FROM patrimonio.bem_responsavel
             WHERE dt_fim is null   \n";
        if ($_REQUEST['inNumResponsavelAnterior']) {
            $stSql.= " AND numcgm = ".$_REQUEST['inNumResponsavelAnterior']." ";
        }

        return $stSql;
    }

    public function recuperaMaxDtInicio(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaMaxDtInicio",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaMaxDtInicio()
    {
        $stSql= "
            SELECT MAX( dt_inicio ) AS dt_inicio
              FROM patrimonio.bem_responsavel   \n";
        if ($_REQUEST['inNumResponsavelAnterior']) {
            $stSql.= " WHERE numcgm = ".$_REQUEST['inNumResponsavelAnterior']." ";
        }

        return $stSql;
    }

}
