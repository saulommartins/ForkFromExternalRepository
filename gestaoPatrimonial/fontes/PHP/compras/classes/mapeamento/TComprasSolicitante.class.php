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
    * Arquivo de mapeamento relacionado aos Solicitantes
    * Data de Criação: 11/02/2008

    * @author Analista: Gelson W
    * @author Luiz Felipe Prestes Teixeira

    * Casos de uso: uc-03.04.34

    $Id: TComprasSolicitante.class.php 59612 2014-09-02 12:00:51Z gelson $

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TComprasSolicitante extends Persistente
{
    /**
     * cgmSolicitante
     * @access public
     * */
    public $cgmSolicitante;

    /**
        * Método Construtor
        * @access Private
    */
    public function TComprasSolicitante()
    {
        parent::Persistente();
        $this->setTabela('compras.solicitante');
        $this->setCampoCod('solicitante');
        $this->setComplementoChave('');
        $this->AddCampo('solicitante' ,'integer',true,'',true,true);
        $this->AddCampo('ativo','boolean',true,'',false,false);
    }

    public function recuperaSolicitantes(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaSolicitantes",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaSolicitantes()
    {
        $stSql = "SELECT solicitante.solicitante
                             , CASE WHEN solicitante.ativo = 't' THEN 'Ativo' ELSE 'Inativo' END AS ativo
                             , sw_cgm.nom_cgm
                          FROM sw_cgm, compras.solicitante
                          WHERE solicitante.solicitante = sw_cgm.numcgm";

        return $stSql;
    }

    public function verificaPodeExcluirSolicitante(&$rsRecordSet,$stFiltro = '',$stOrder = '',$boTransacao = '')
    {
        return $this->executaRecupera("verificaSolicitanteExclusao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function verificaSolicitanteExclusao()
    {
        $stSql = "SELECT 1
                          FROM compras.solicitacao
                          WHERE solicitacao.cgm_solicitante =".$this->cgmSolicitante;

        return $stSql;
    }

    public function verificaPodeInserirSolicitante(&$rsRecordSet,$stFiltro = '',$stOrder = '',$boTransacao = '')
    {
        return $this->executaRecupera("verificaSolicitanteInserir",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function verificaSolicitanteInserir()
    {
        $stSql = "SELECT 1
                          FROM compras.solicitante
                          WHERE solicitante.solicitante =".$this->cgmSolicitante;

        return $stSql;
    }

    public function setCgmSolicitacao($valor)
    {
        $this->cgmSolicitante = $valor;
    }
}
