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
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage

    * $Id: ITextBoxSelectBanco.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.05.01
                    uc-05.05.10

*/

/*
$Log$
Revision 1.2  2006/09/15 14:46:06  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once ( CLA_SELECT );

class ITextBoxSelectBanco extends TextBoxSelect
{
    public $boVinculoPlanoBanco;

    public function ITextBoxSelectBanco()
    {
        parent::TextBoxSelect();

        $this->setRotulo              ( "Banco"                );
        $this->setName                ( "inCodBanco"                );
        $this->setTitle               ( "Selecione o banco."    );

        $this->obTextBox->setRotulo              ( "Banco"                );
        $this->obTextBox->setTitle               ( "Selecione o banco."    );
        $this->obTextBox->setName                ( "inCodBancoTxt"        );
        $this->obTextBox->setId                  ( "inCodBancoTxt"        );
        $this->obTextBox->setSize                ( 12                     );
        $this->obTextBox->setMaxLength           ( 10                      );
        $this->obTextBox->setInteiro             ( true                   );

        $this->obSelect->setRotulo              ( "Banco"                         );
        $this->obSelect->setName                ( "inCodBanco"                    );
        $this->obSelect->setId                  ( "inCodBanco"                    );
        $this->obSelect->setCampoID             ( "num_banco"                     );
        $this->obSelect->setCampoDesc           ( "nom_banco"                     );
        $this->obSelect->addOption              ( "", "Selecione"                 );
        $this->obSelect->setStyle               ( "width: 200px"                  );

        $this->boVinculoPlanoBanco = false;
    }

    public function setNumBanco($stNumBanco)
    {
       $this->stNumBanco = $stNumBanco;
    }

    public function montaHTML()
    {
        include_once(CAM_GT_MON_MAPEAMENTO . "TMONBanco.class.php");
        $obTMapeamento          = new TMONBanco();
        $rsRecordSet            = new Recordset;
        if ($this->boVinculoPlanoBanco) {
            $stFiltro = " WHERE EXISTS ( SELECT 1
                                         FROM contabilidade.plano_banco
                                        WHERE banco.cod_banco = plano_banco.cod_banco
                                          AND plano_banco.exercicio = '" . Sessao::getExercicio() . "'
                                     ) ";
        }
        $obTMapeamento->recuperaTodos($rsRecordSet,$stFiltro,' ORDER BY num_banco');

        $this->obSelect->preencheCombo          ( $rsRecordSet                    );
        if ($this->stNumBanco != "") {
           $this->obTextBox->setValue($this->stNumBanco);
           $this->obSelect->setValue($this->stNumBanco);
        }
        parent::montaHTML();
    }
}
?>
