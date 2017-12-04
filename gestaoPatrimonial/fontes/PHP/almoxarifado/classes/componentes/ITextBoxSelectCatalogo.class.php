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
 * Arquivo de popup de busca de CGM
 * Data de Criação: 27/02/2003

 * @author Analista: Diego Barbosa Victoria
 * @author Desenvolvedor: Diego Barbosa Victoria

 * @package URBEM
 * @subpackage

 * Casos de uso: uc-03.03.04
                 uc-03.04.03

 $Id: ITextBoxSelectCatalogo.class.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once CLA_TEXTBOX_SELECT;

class ITextBoxSelectCatalogo extends TextBoxSelect
{
    public $boPermiteManutencao;
    public $apenasComClassificacao;

    public function ITextBoxSelectCatalogo()
    {
        parent::TextBoxSelect();

        $this->boNaoPermiteManutencao = false;
        $this->apenasComClassificacao = false;

        $this->setRotulo              ( "Catálogo"              );
        $this->setName                ( "inCodCatalogo"         );
        $this->setTitle               ( "Selecione o catálogo." );

        $this->obTextBox->setRotulo    ( "Catálogo"             );
        $this->obTextBox->setTitle     ( "Selecione o catálogo.");
        $this->obTextBox->setName      ( "inCodCatalogoTxt"     );
        $this->obTextBox->setId        ( "inCodCatalogoTxt"     );
        $this->obTextBox->setSize      ( 6                      );
        $this->obTextBox->setMaxLength ( 3                      );
        $this->obTextBox->setInteiro   ( true                   );
        $this->obTextBox->setNull      ( false                  );

        $this->obSelect->setRotulo    ( "Catálogo"      );
        $this->obSelect->setName      ( "inCodCatalogo" );
        $this->obSelect->setId        ( "inCodCatalogo" );
        $this->obSelect->setStyle     ( "width: 200px"  );
        $this->obSelect->setCampoID   ( "cod_catalogo"  );
        $this->obSelect->setCampoDesc ( "descricao"     );
        $this->obSelect->addOption    ( "", "Selecione" );
        $this->obSelect->setNull      ( false           );
    }

    public function montaHTML()
    {
    include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogo.class.php";
        $stFiltro = "";
        $obTMapeamento = new TAlmoxarifadoCatalogo();
        $rsRecordSet   = new Recordset;

        if ($this->apenasComClassificacao == true) {
               $stFiltro = " WHERE  cod_catalogo IN
                            (
                               SELECT  DISTINCT(cc.cod_catalogo) AS codigo
                                 FROM  almoxarifado.catalogo c
                                    ,  almoxarifado.catalogo_classificacao cc
                                WHERE  c.cod_catalogo = cc.cod_catalogo
                            )";
        }

    if (!$this->boNaoPermiteManutencao) {
           $obTMapeamento->recuperaRelacionamento($rsRecordSet,$stFiltro, '');
        } else {
           $obTMapeamento->recuperaTodos($rsRecordSet, $stFiltro, '');
        }

        while (!$rsRecordSet->eof()) {
            Sessao::write("nomFiltro['catalogo'][".$rsRecordSet->getCampo( 'cod_catalogo' )."]", $rsRecordSet->getCampo( 'descricao' ));
            $rsRecordSet->proximo();
        }

        $rsRecordSet->setPrimeiroElemento();

        $this->obSelect->preencheCombo( $rsRecordSet );
        parent::montaHTML();
    }

    public function setNaoPermiteManutencao($valor) { $this->boNaoPermiteManutencao = $valor; }
    public function setApenasComClassificacao($valor) { $this->apenasComClassificacao = $valor; }
    public function setCodCatalogo($valor) { $this->obTextBox->setValue($valor); $this->obSelect->setValue($valor); }

}

?>
