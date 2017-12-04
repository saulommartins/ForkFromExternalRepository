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
    * Arquivo que monta os combos de banco e agência
    * Data de Criação: 27/02/2003

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage

    * $Id: IMontaAgencia.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.05.02, uc-05.05.10

*/

class IMontaAgencia extends Objeto
{
    public $obITextBoxSelectBanco;
    public $obTextBoxSelectAgencia;
    public $stNumAgencia;
    public $boVinculoPlanoBanco;
    public $inCodEntidadeVinculo;

    public function IMontaAgencia()
    {
        include_once(CAM_GT_MON_COMPONENTES. "ITextBoxSelectBanco.class.php");
        $this->obITextBoxSelectBanco = new ITextBoxSelectBanco;

        $this->obTextBoxSelectAgencia = new TextBoxSelect;
        $this->obTextBoxSelectAgencia->setRotulo              ( "Agência"                );
        $this->obTextBoxSelectAgencia->setName                ( "stNumAgencia"               );
        $this->obTextBoxSelectAgencia->setTitle               ( "Selecione a agência."    );
        $this->obTextBoxSelectAgencia->obTextBox->setRotulo   ( "Agência"              );
        $this->obTextBoxSelectAgencia->obTextBox->setTitle    ( "Selecione a agência."  );
        $this->obTextBoxSelectAgencia->obTextBox->setName     ( "stNumAgenciaTxt"        );
        $this->obTextBoxSelectAgencia->obTextBox->setId       ( "stNumAgenciaTxt"        );
        $this->obTextBoxSelectAgencia->obTextBox->setSize     ( 12                     );
        $this->obTextBoxSelectAgencia->obTextBox->setMaxLength( 10                      );
        $this->obTextBoxSelectAgencia->obTextBox->setInteiro  ( false );
        $this->obTextBoxSelectAgencia->obTextBox->setCaracteresAceitos( "[0-9-]" );

        $this->obTextBoxSelectAgencia->obSelect->setRotulo    ( "Agência"                        );
        $this->obTextBoxSelectAgencia->obSelect->setName      ( "stNumAgencia"                  );
        $this->obTextBoxSelectAgencia->obSelect->setId        ( "stNumAgencia"                  );
        $this->obTextBoxSelectAgencia->obSelect->setCampoID   ( "num_agencia"                     );
        $this->obTextBoxSelectAgencia->obSelect->setCampoDesc ( "nom_agencia"                     );
        $this->obTextBoxSelectAgencia->obSelect->addOption    ( "", "Selecione"                 );
        $this->obTextBoxSelectAgencia->obSelect->setStyle     ( "width: 200px"                    );
        $this->obTextBoxSelectAgencia->obSelect->setDependente (true);

        $this->boVinculoPlanoBanco = false;
   }

   public function setNumAgencia($stNumAgencia)
   {
      $this->stNumAgencia = $stNumAgencia;
   }

   public function geraFormulario(&$obFormulario)
   {
       if ($this->stNumAgencia != "") {
          include_once(CAM_GT_MON_MAPEAMENTO . "TMONBanco.class.php");
          $obTMapeamento          = new TMONBanco();
          $rsBanco                = new Recordset;
          $stFiltro = ' where num_banco = \''.$this->obITextBoxSelectBanco->obTextBox->getValue().'\'';
          $obTMapeamento->recuperaTodos($rsBanco, $stFiltro);
          include_once(CAM_GT_MON_MAPEAMENTO . "TMONAgencia.class.php");
          $obTMapeamento          = new TMONAgencia();
          $rsRecordSet            = new Recordset;
          $stFiltro = ' where cod_banco = '.$rsBanco->getCampo('cod_banco');
          $obTMapeamento->recuperaTodos($rsRecordSet, $stFiltro, ' ORDER BY num_agencia');
          $this->obTextBoxSelectAgencia->obSelect->preencheCombo($rsRecordSet);
          $this->obTextBoxSelectAgencia->obTextBox->setValue($this->stNumAgencia);
          $this->obTextBoxSelectAgencia->obSelect->setValue($this->stNumAgencia);
       }
       $pgOcul  = CAM_GT_MON_INSTANCIAS.'agenciaBancaria/OCMontaAgencia.php?'.Sessao::getId();
       if ($this->boVinculoPlanoBanco) {
            $this->obITextBoxSelectBanco->boVinculoPlanoBanco = true;
            $pgOcul .= "&boVinculoPlanoBanco=true";
       }
       $stOnChange = "ajaxJavaScript('".$pgOcul."&stNumBanco='+this.value,'PreencheAgencia');";
       $this->obITextBoxSelectBanco->obTextBox->obEvento->setOnChange($stOnChange);
       $this->obITextBoxSelectBanco->obSelect->obEvento->setOnChange($stOnChange);

       $obFormulario->addComponente($this->obITextBoxSelectBanco);
       $obFormulario->addComponente($this->obTextBoxSelectAgencia);
   }

}
?>
