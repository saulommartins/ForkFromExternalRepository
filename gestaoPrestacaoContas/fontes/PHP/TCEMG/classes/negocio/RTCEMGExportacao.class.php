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
 * Classe de regra de exportacao TCE/MG
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id: RTCEMGExportacao.class.php 63325 2015-08-18 17:13:32Z franver $
 * $Rev: 63325 $
 * $Author: franver $
 * $Date: 2015-08-18 14:13:32 -0300 (Tue, 18 Aug 2015) $
 */

include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoDespesa.class.php';

class RTCEMGExportacao
{
    public $stPoder;
//        $obTransacao;

    /**
     * Método contrutor, instancia as classes necessarias.
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
//        $this->obTransacao                              = new Transacao                             ();
    }

    /**
     * listEntidadePoder, recupera todas as entidades de acordo com o poder
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function listEntidadePoder(&$rsEntidades)
    {
        if ($this->stPoder == 'legislativo') {
            $stFiltro = " AND (nom_cgm ILIKE '%camara%' OR nom_cgm ILIKE '%câmara%') ";
        }
        
        $obROrcamentoEntidade = new ROrcamentoEntidade();
        $obROrcamentoEntidade->obRCGM->setNumCGM     (Sessao::read('numCgm'));
        $obROrcamentoEntidade->listarUsuariosEntidade($rsEntidades, $stFiltro . ' ORDER BY cod_entidade');
    }
}
