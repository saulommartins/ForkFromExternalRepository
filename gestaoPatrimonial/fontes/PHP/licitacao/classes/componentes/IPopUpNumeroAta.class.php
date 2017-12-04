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
    * Arquivo de popup de busca Número Ata.
    * Data de Criação: 14/01/2009

    * @author Analista:      Gelson Gonçalves
    * @author Desenvolvedor: Diogo Zarpelon

    $Id:$

    */

include_once CLA_BUSCAINNER;

class IPopUpNumeroAta extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */
    public $obForm;
    /**
        * Metodo Construtor
        * @access Public

    */
    public function IPopUpNumeroAta($obForm)
    {
        parent::BuscaInner();

        $pgOcul = CAM_GP_LIC_PROCESSAMENTO.'OCPopUpNumeroAta.php';

        $inLastAta     = SistemaLegado::pegaDado("num_ata", "licitacao.ata", " ORDER BY num_ata DESC LIMIT 1");
        $stSizeMascara = (!empty($inLastAta) ? strlen($inLastAta) : 1);
        $stMascara     = str_pad($stMascara , $stSizeMascara , '9')."/9999";

        $this->obForm = $obForm;
        $this->setRotulo               ( 'Número da Ata'    );
        $this->setTitle                ( 'Selecione o número da ata.' );
        $this->setId                   ( 'NumeroAta'        );
        $this->setMostrarDescricao	   ( false              );

        $this->setCampoCod             ( ""                 );
        $this->setCampoCod			   ( new TextBox()      );
        $this->obCampoCod->setMinLength( 1                  );
        $this->obCampoCod->setRotulo   ( $this->getRotulo() );
        $this->obCampoCod->setName     ( "stNumeroAta"      );
        $this->obCampoCod->setMascara  ( $stMascara         );
        $this->obCampoCod->obEvento->setOnChange(" if (this.value != '') {this.value = preencheProcessoComZeros( this.value,'".$stMascara."', '".Sessao::getExercicio()."');} ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stNumAta='+this.value+'&stField='+this.name, 'validaNumAta');");
    }

    public function montaHTML()
    {
        $this->setFuncaoBusca("abrePopUp('".CAM_GP_LIC_POPUPS."processoLicitatorio/FLBuscaNumeroAta.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId . "','". $this->stTipo . "','" . Sessao::getId() ."','800','550');");
        parent::montaHTML();
    }
}

?>
