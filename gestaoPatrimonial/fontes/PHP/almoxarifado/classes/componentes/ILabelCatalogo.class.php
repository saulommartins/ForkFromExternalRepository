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
    * Componente Label do Catálogo
    * Data de Criação: 24/07/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-03.03.04
                    uc-03.04.03
*/

/*
$Log$
Revision 1.2  2006/09/21 17:48:08  fernando
Inclusão da sessao->nomFiltro com o nome dos campos para serem utilizados nos filtros.

Revision 1.1  2006/07/26 13:31:45  leandro.zis
Bug #6623#

*/

include_once( CLA_LABEL );

class ILabelCatalogo extends Label
{
    public $inCodCatalogo;
    public $boMostraCodigo = true;

    public function ILabelCatalogo()
    {
        parent::Label();

        $this->setRotulo ("Catálogo" );
        $this->setName   ("stCatalogo" );
        $this->setId     ("stCatalogo" );
    }

    public function setCodCatalogo($value)
    {
        $this->inCodCatalogo = $value;
    }

    public function getCodCatalogo()
    {
        return $this->inCodCatalogo;
    }

    public function setMostraCodigo($valor)
    {
        $this->boMostraCodigo = $valor;
    }

    public function montaHTML()
    {
        if ( $this->getCodCatalogo() ) {
           include_once(CAM_GP_ALM_MAPEAMENTO . "TAlmoxarifadoCatalogo.class.php");
           $obTMapeamento          = new TAlmoxarifadoCatalogo();
           $rsRecordSet            = new Recordset;
           $obTMapeamento->setDado("cod_catalogo", $this->getCodCatalogo());
           $obTMapeamento->recuperaPorChave($rsRecordSet);

           if ($this->boMostraCodigo) {
               $this->setValue ( $rsRecordSet->getCampo('cod_catalogo').'-'.$rsRecordSet->getCampo('descricao') );
           } else {
               $this->setValue ( $rsRecordSet->getCampo('descricao') );
           }
        } else {
           $this->setValue('&nbsp;');
        }

        parent::montaHTML();

    }
}
?>
