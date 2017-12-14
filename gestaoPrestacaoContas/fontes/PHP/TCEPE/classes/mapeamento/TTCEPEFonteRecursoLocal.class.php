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
    * Classe de mapeamento da tabela tcepe.fonte_recurso_local
    * Data de Criação   : 30/09/2014

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos
    *
    $Id: TTCEPEFonteRecursoLocal.class.php 60373 2014-10-16 14:35:21Z diogo.zarpelon $
    *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEFonteRecursoLocal extends Persistente
{
    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPEFonteRecursoLocal()
    {
        parent::Persistente();
        $this->setTabela('tcepe.fonte_recurso_local');

        $this->setCampoCod('cod_fonte');
        $this->setComplementoChave('exercicio, cod_entidade, cod_local');

        $this->AddCampo('cod_fonte'      , 'integer', true , ''  , true, true);
        $this->AddCampo('exercicio'      , 'varchar', true , '4' , true, true);
        $this->AddCampo('cod_entidade'   , 'integer', true , ''  , true, true);
        $this->AddCampo('cod_local'      , 'integer', true , ''  , true, true);
    }

    public function recuperaLocalSelecionados(&$rsRecordset, $stFiltro = "", $stOrdem = "")
    {
        $obErro = $this->executaRecupera("montaRecuperaLocalSelecionados", $rsRecordset, $stFiltro, $stOrdem);
        return $obErro;
    }

    public function montaRecuperaLocalSelecionados()
    {
        $stSql = "
            SELECT local.* 

             FROM tcepe.fonte_recurso_local

       INNER JOIN organograma.local
               ON fonte_recurso_local.cod_local = local.cod_local
       
            WHERE 1=1 ";
    
            if ($this->getDado('cod_fonte')) {
                $stSql .= " AND fonte_recurso_local.cod_fonte = ".$this->getDado('cod_fonte');
            }
        
            if ($this->getDado('exercicio')) {
                $stSql .= " AND fonte_recurso_local.exercicio = '".$this->getDado('exercicio')."'";
            }

            if ($this->getDado('cod_entidade')) {
                $stSql .= " AND fonte_recurso_local.cod_entidade = ".$this->getDado('cod_entidade');
            }

        $stSql .= " ORDER BY UPPER(descricao) ASC ";

        return $stSql;
    }

    public function recuperaLocalDisponiveis(&$rsRecordset, $stFiltro = "", $stOrdem = "")
    {
        $obErro = $this->executaRecupera("montaRecuperaLocalDisponiveis", $rsRecordset, $stFiltro, $stOrdem);
        return $obErro;
    }

    public function montaRecuperaLocalDisponiveis()
    {
        $stSql = "
            SELECT local.*

              FROM organograma.local 

             WHERE NOT EXISTS (
                SELECT 1
                  
                  FROM tcepe.fonte_recurso_local
                 
                 WHERE fonte_recurso_local.cod_local = local.cod_local ";

            if ($this->getDado('cod_fonte')) {
                $stSql .= " AND fonte_recurso_local.cod_fonte = ".$this->getDado('cod_fonte');
            }
        
            if ($this->getDado('exercicio')) {
                $stSql .= " AND fonte_recurso_local.exercicio = '".$this->getDado('exercicio')."'";
            }

            if ($this->getDado('cod_entidade')) {
                $stSql .= " AND fonte_recurso_local.cod_entidade = ".$this->getDado('cod_entidade');
            }
        
        $stSql .= " ) 
            ORDER BY UPPER(descricao) ASC ";

        return $stSql;
    }



}